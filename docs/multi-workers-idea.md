# Multi-worker parallel execution — design idea

> **Status:** proposal / idea only. No code exists for this yet.
> This document sketches how Probatio could run test files in parallel worker
> processes, using only the PHP standard library: `proc_open()` to spawn workers
> and `stream_select()` to multiplex their output in the main process.

The goal is to cut wall-clock time for large suites while preserving the
zero-dependency, PHP >= 7.2 constraints and the existing BDD API.

---

## 1. Why this fits the current architecture

Probatio already has the right seam for parallelism: the **two-phase model**
(`docs/architecture.md` §1).

| Phase | Today | In the parallel design |
| --- | --- | --- |
| **Registration** | `TestRegistry` requires every test file and builds the `TestFile → TestGroup → TestItem` tree in a single process. | Stays in the **main** process for discovery + shuffle; workers **re-require** only the files assigned to them. |
| **Execution** | `SuiteRunner → FileRunner → GroupRunner → ItemRunner` walks the tree inline. | Moves into **worker** processes, one runner chain per file. The main process only multiplexes results. |

The key consequence: **closures cannot cross IPC**. A `\Closure` cannot be
serialized, so the distribution unit is the *file path*. Each worker rebuilds its
own tree by requiring the files it is given — the same way a regular run already
rebuilds the tree on every invocation.

This keeps the existing runners (`FileRunner`, `GroupRunner`, `ItemRunner`,
`HookRunner`) reusable almost unchanged inside the worker. What changes is *where*
they run and *how their output is routed*.

---

## 2. High-level picture

```
[ main process ]                     bin/probatio -> Cli::run()
   │  · discover test files (Path::getFilesRecursive)
   │  · build the file list, shuffle (same as SuiteRunner today)
   │
   │  proc_open()  x  N workers
   ├─► Worker 1   ── requests:  stdin   (fd 0 / dedicated fd 3)
   ├─► Worker 2   ── events:    stdout  (fd 1 / dedicated fd 3)
   └─► Worker N
   │
   │  loop: stream_select({w1.read, w2.read, …})
   │        → aggregate events into TestStats
   │        → render via Printer (streaming or at the end)
   └─> exit(0 | 1) based on aggregated counters
```

A worker is the **same binary** launched in a special mode (e.g.
`bin/probatio --worker`). It initializes Composer's autoloader, registers the
test files it was told to run, executes them with the existing runner chain, and
emits structured events on its output pipe instead of printing inline.

---

## 3. Spawning workers with `proc_open()`

`proc_open()` is available since PHP 4.3 and satisfies the PHP >= 7.2 floor.
Standard descriptor spec:

```php
$spec = [
    0 => ['pipe', 'r'],  // child stdin  -> main writes work requests
    1 => ['pipe', 'w'],  // child stdout -> main reads result events
    2 => ['file', 'php://stderr', 'w'], // child stderr passes through
    3 => ['pipe', 'w'],  // optional dedicated control channel (see §5)
];
```

Getting the worker process’s stdin back (`$pipes[0]`) requires `proc_open()`
(not `popen()`/`exec()`), which is exactly what makes round-trip
request/response possible.

Because a worker is spawned from the framework's own binary, PHP's `stream_select()`,
`proc_terminate()`, and `proc_close()` are the only primitives needed — no
external process manager, no extra dependency.

---

## 4. `stream_select()` event loop in the main process

The main process never blocks on a single worker. Instead it keeps an array of
readable pipes (one per `$pipes[1]`):

```php
$readers = [];
foreach ($workers as $id => $worker) {
    stream_set_blocking($worker->read, false);
    $readers[$id] = $worker->read;
}

while ($pending > 0) {
    $r = $readers;
    $w = null;
    $e = null;
    $n = stream_select($r, $w, $e, $timeout);

    if ($n === false) {
        // interrupted by signal or pipe error
    }
    foreach ($r as $stream) {
        $id = array_search($stream, $readers, true);
        consume($workers[$id]);
    }
    // optional: check `now - started` against per-test or global timeout
}
```

Rules that make the loop robust:

- **Non-blocking reads** — `stream_set_blocking(..., false)` so one chatty
  worker cannot stall the loop.
- **Framing** — events are newline-delimited JSON (or length-prefixed). The
  reader keeps a residual buffer and splits on the frame terminator, so a frame
  is never processed half-arrived. Interleaved frames from different workers are
  naturally independent because each worker owns its own stream.
- **Idle detection** — `stream_select` returns `0` on timeout; use that to drive
  watchdog logic (killing hung workers per §8).
- **EOF handling** — when `feof($stream)` becomes true the worker exited;
  compare its aggregated counters and exit status against the events it sent
  before considering the batch complete.

---

## 5. Wire protocol

Two channels are contemplated. Both keep the zero-dependency rule (JSON is
built into PHP).

### Channel A — stdout carries both protocol and test output

The worker’s test code may call `Printer` (which writes to `STDOUT`), and user
code may `echo` freely. Mixing framed control data with free-form output on the
same stream makes robust parsing impossible.

Therefore **protocol messages must be identifiable** at all times. Either:

1. **Only the worker writes to stdout**, wrapping every piece of user-facing
   output in a `log` event (the worker captures/re-routes its own `Printer`
   output into events); or
2. Use **Channel B**.

### Channel B — dedicated control descriptor (fd 3)

Add a third, reserved descriptor:

```
3 => ['pipe', 'w']   // JSON-line protocol, strict framing
```

- `stdout` (fd 1): stays free for user-visible test output; the main process
  relays it verbatim as it arrives (detect display of raw output with an
  envelope absent for backwards compatibility with *sequential* runs).
- `stderr` (fd 2): passes through for PHP warnings/errors.
- fd 3: the only framed channel. No user code ever touches it.

Channel B keeps the protocol strict and the relay trivial. **Recommendation: use
Channel B**, with Channel A as a fallback where fd 3 is unavailable (constrained
platforms).

### Message shapes

```
worker → main (all on the framed channel)

{"type":"ready","pid":n}                        # booted, autoload done, awaiting files
{"type":"file-start","file":"tests/Unit/CalcTest.php"}
{"type":"assert","file":..,"ok":true/false}     # per assertion (aggregation)
{"type":"test","file":..,"name":"sum",
  "status":"ok|err","error":{"class":..,"message":..,"file":..,"line":n},
  "ok_asserts":n,"err_asserts":m,"took_ms":t}
{"type":"file-end","file":..,"ok_tests":n,"err_tests":m}
{"type":"log","level":"info|ok|err|group|item","message":"...","line":...}
{"type":"done"}
```

```
main → worker (stdin)

{"type":"run","files":["...","..."]}            # batch assignment
{"type":"cancel"}                               # graceful shutdown
{"type":"bye"}                                  # no more work, exit cleanly
```

`TestStats` today is a mutable singleton incremented inline during execution
(`ItemRunner`, `Assertions::process()`). In the parallel design the counters move
to the **main process** and become derivable from `assert`/`test` events, while
workers emit events instead of incrementing the shared counters. This is the
largest behavioral change to the internals (see §7).

---

## 6. Distribution strategy

### 6.1 File-level (recommended)

Distribute **TestFile paths** — the natural unit, since `SuiteRunner` already
iterates files and shuffles them. Two scheduling policies:

- **Static split** — divide `$files` into N contiguous chunks after shuffle.
  Simple, but the slowest file in a chunk gates that worker.
- **Dynamic pull (better)** — start each worker with roughly
  `ceil(count/N)` files, then whenever a worker reports `file-end` and a ready
  signal, hand it the next unassigned file from a shared queue.
  This load-balances automatically on suite speed variance.

The pull model also couples naturally with `proc_open`’s bidirectional pipes:
the main process has the queue in memory and writes the next `run` frame right
after reading a `file-end`.

### 6.2 Group/item-level (future)

Distributing `TestGroup`/`TestItem` sub-trees would allow work stealing *within*
a file, but it requires serializing the definition tree (name/path/hook metadata)
over IPC and re-materializing it, while closures still cannot travel. Hooks would
need explicit policy on where `beforeAll`/`afterAll` operate (per-worker).
Defer until file-level parallelism is proven.

---

## 7. Impact on existing internals

| Component | Change |
| --- | --- |
| `Cli::run()` | detect `--worker` mode and route to the worker entry instead of the suite runner. |
| `TestSuite` / `TestRegistry` | unchanged for discovery; worker mode requires a slim `Worker` entry that registers only its assigned files. |
| `SuiteRunner` | replaced in main by the worker pool + event loop; reuse its **shuffle** and file iteration logic as the queue source. |
| `FileRunner`/`GroupRunner`/`ItemRunner`/`HookRunner` | reused **as-is** inside the worker; however their direct `Printer` calls become *events* in worker mode (a thin output adapter selected by config). |
| `TestStats` | counters move to the main aggregator, fed by worker events. Workers no longer own authoritative counters. Single-process runs can keep the existing inline implementation. |
| `Printer` | gets an output adapter seam: terminal writer (today) vs event emitters (worker). |
| `TestCase` / hooks / `assigns` | unchanged in code; but see §7.1 semantics. |

### 7.1 Hook and state semantics across processes

- **State is not shared.** `$this->set()`/`let()`/`assigns` in the parent process
  never reach a worker. `beforeAll` runs per worker (per file), which is the
  current per-file semantics anyway — but any cross-file expectations about
  shared in-memory state must be revisited.
- **Cross-process fixtures** (files, network services) remain valid because they
  live outside PHP memory; processes only share the filesystem/OS.
- **Ordering** — main output order now depends on completion order, not
  declaration order. This matches the existing suite shuffle philosophy
  (order-independence as a feature).

---

## 8. Failure handling, timeouts, cancellation

- **Hung worker** — because `stream_select` reports no activity on a worker’s
  stream, the idle watchdog can flag it. Policy:
  1. wait grace period (configurable),
  2. `stream_select` timeout → log a `log` event,
  3. still unresponsive → `proc_terminate()` (SIGTERM), escalate to SIGKILL after
     a second grace, then `proc_close()`. The worker’s running test counts as
     failed via the watchdog, not via an event.
- **Crash** — EOF on the worker stream with fewer `file-end` events than files
  sent ⇒ mark the missing files/interrupted tests as failed and continue with the
  remaining workers.
- **SIGINT/SIGTERM on main** — forward a `cancel` frame to all workers, then
  `proc_terminate()` them so no child process survives; honor the exit code of
  the interrupted run.
- **Timeouts** — optional per-test and per-file timeout enforced in the main
  loop by comparing `microtime(true)` snapshots against timestamps stored at
  `file-start`.

---

## 9. Configuration

Follow the existing env-var convention (`Suite\Config` + `Utils\Env`):

| Variable | Default | Purpose |
| --- | --- | --- |
| `PROBATIO_WORKERS` | `0` (sequential) | number of worker processes (`0`/`1` = current behavior) |
| `PROBATIO_WORKER_MAX_GRACE` | `10` | seconds the watchdog waits before terminating a silent worker |
| `PROBATIO_WORKER_FILE_TIMEOUT` | `0` (off) | per-file hard timeout in seconds |
| `PROBATIO_WORKER_MODE` | `off` | internal: set to `on` by the `--worker` invocations |

Worker re-spawn of the binary reuses `bin/probatio` itself, so no new
distribution artifact is required.

---

## 10. Edge cases & concerns

- **Windows** — `proc_open()` pipes work, but `stream_select()` on pipes has
  historically been unreliable on Windows (it mainly supported sockets). Target
  POSIX first (CI is `ubuntu-22.04`); document Windows as best-effort.
- **Output interleaving** — with Channel B the main process relays each worker’s
  stdout separately; frames are per-worker so no torn lines. With Channel A,
  user `echo` must be captured by the worker and emitted as `log` frames.
- **Stdout pollution by third-party libs** — anything the worker prints outside
  the protocol must be tolerated (frame parser skips/relays non-JSON lines in
  Channel A / ignores fd 1 in Channel B).
- **Resource use** — N workers = N PHP runtimes + N copies of the autoloader.
  For tiny suites this is pure overhead; keep `PROBATIO_WORKERS=0` default and
  only engage for large suites.
- **Re-entrant registration** — `tests.php` bootstrap (`PROBATIO_MAIN_FILE`)
  runs in each worker as well, keeping behavior identical to a sequential run.

---

## 11. Alternatives considered

- **`pcntl_fork()`** — shares memory (no IPC needed) but is POSIX-only, unstable
  with singletons like `TestSuite::$instance`, and unavailable in the PHP 7.2
  CI/Windows target. Rejected.
- **Threads / pthreads** — not available for PHP 7.2+ CLI mainstream. Rejected.
- **Message queue / sockets** — unnecessary external state; pipes are free.
- **External runners (GNU parallel, xargs, fastly compute)** — violate the
  zero-dependency, single-binary promise. Rejected.

---

## 12. Open questions

1. Should the main process stream results (live terminal) or buffer and print at
   the end (stable, deterministic report)? Streaming is closer to the current UX.
2. Per-file parallelism first, or a later group-level work-stealing mode (§6.2)?
3. Should `PROBATIO_WORKERS` default to CPU core count (`nproc`-like) when set
   without a number?
4. Interaction with `examples/buggy_tests`: parallel runs must still exit `1`
   when any worker reports failures — easy via aggregation, but worth a fixture
   test.

---

## 13. Relation to existing docs

- `docs/architecture.md` §1/§3.2 describes the two-phase model and runner chain
  that this design reuses.
- `docs/future-improvements.md` — this document is a more concrete design
  proposal; the two can be linked.
