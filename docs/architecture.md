# Architecture

Probatio is a **zero-dependency, self-contained PHP testing framework** (PHP >= 7.2).
It exposes a Pest/JS-like BDD API — `describe()`, `context()`, `test()`, `it()`,
`expect()`, and lifecycle hooks — without relying on PHPUnit or any other package.

This document describes the internal architecture: how the framework is bootstrapped,
how test files are discovered and registered, how suites/group/items are executed,
and the design decisions behind each component.

---

## 1. High-level flow

```
bin/probatio
  └─ autoload (Composer, PSR-4)
      └─ Cli::run()
          └─ probatio()                       # TestSuite::getInstance() — singleton
              ├─ registerTestFiles()          # PHASE 1: discovery + registration
              │    ├─ mainFile require (optional bootstrap)
              │    ├─ recursive scan of PROBATIO_TESTS_DIR
              │    └─ require of each test file → tree built in TestRegistry
              └─ run()                        # PHASE 2: execution
                   ├─ runRegisteredTests()
                   │    └─ SuiteRunner::run()  # files shuffled
                   │         └─ FileRunner → GroupRunner (recursive) → ItemRunner
                   ├─ printSummary()           # TestStats → exit code
                   └─ exit(1) on failure
```

The framework is intentionally built on a **two-phase model**:

1. **Registration phase** — test files are `require`d. The global/namespaced API
   functions call back into a `TestRegistry`, which builds an in-memory tree of
   *definitions* (`TestFile → TestGroup → TestItem`). No test code runs here.
2. **Execution phase** — a chain of *runners* walks the tree level by level,
   invoking lifecycle hooks and test closures, and accumulates results in `TestStats`.

---

## 2. Directory layout

```
src/Probatio/
├── Cli.php               # CLI bootstrap: prints version, drives the suite
├── Functions.php         # Namespaced API: describe, test, it, expect, hooks, probatio()
├── GlobalFunctions.php   # conditionally registers global wrappers (Composer "files")
├── Checks/
│   ├── AssertionError.php  # exception thrown by a failed assertion
│   ├── Assertions.php      # trait with assertSame/assertTrue/... injected into TestCase
│   └── Expectation.php     # fluent expect(...)->toBe(...) API with `->not` inversion
├── Definitions/
│   ├── Definition.php      # marker interface (TestGroup | TestItem)
│   ├── TestCase.php        # runtime context bound as `$this` in test closures
│   ├── TestFile.php        # one test file + lazily created root group
│   ├── TestGroup.php       # describe/context block — tree node with hooks + children
│   ├── TestHook.php        # beforeAll / afterAll / beforeEach / afterEach
│   └── TestItem.php        # a single test()/it() case
├── Runners/
│   ├── Runnable.php        # interface: run(TestCase)
│   ├── RunnerException.php
│   ├── SuiteRunner.php     # top level: shuffles and runs TestFile*s
│   ├── FileRunner.php      # runs one TestFile's root group
│   ├── GroupRunner.php     # runs a TestGroup: hooks + nested groups/items
│   ├── ItemRunner.php      # runs a single TestItem
│   └── HookRunner.php      # runs one lifecycle hook
├── Suite/
│   ├── Config.php          # env-var configuration
│   ├── TestRegistry.php    # registration facade; holds TestFile*s
│   ├── TestStats.php       # ok/err counters for tests and assertions
│   └── TestSuite.php       # singleton orchestrator
└── Utils/
    ├── Caller.php          # backtrace inspection → calling file:line
    ├── Env.php             # env helpers (getStr / getBool)
    ├── Location.php        # file:line value object (fun/caller/exception)
    ├── Path.php            # recursive discovery + test-file matching
    ├── Printer.php         # indented, color/emoji terminal output
    └── Str.php             # str_starts_with/str_ends_with polyfills (PHP < 8)
```

---

## 3. Core concepts

### 3.1 Definitions — the static test tree

Definitions are plain data objects built during registration. They hold no runtime
state, which is what makes the two-phase design possible.

- **`TestFile`** — the root of the tree for one file. Holds a lazily-created
  `rootGroup` and a `currentGroup` pointer used during registration. It provides
  `registerGroup()`, `registerTestItem()`, and `registerHook()`, which mutate the
  group that is *currently* being built.
- **`TestGroup`** — a `describe`/`context` block. Contains:
  - `hooks`: four buckets keyed by type (`before_all`, `after_all`,
    `before_each`, `after_each`);
  - `nodes`: an ordered array of child `TestGroup`s and `TestItem`s.
- **`TestItem`** — a single test from `test()` or `it()`. Stores the name, the
  closure, and the `Location` where it was declared.
- **`TestHook`** — one lifecycle hook; validates its type against
  `TestHook::ALLOWED_TYPES`.
- **`TestCase`** — *not* a static definition; created at runtime per test context
  (see §5).

`Location` is captured for every group/item/hook at construction time via
reflection of the closure or the caller backtrace, so failures can be reported
with precise `file:line` references.

### 3.2 Runners — the execution chain

Every runner implements `Runnable::run(TestCase $tc)`. The hierarchy mirrors the
definition tree exactly:

```
SuiteRunner   (iterates shuffled TestFile*s)
 └─ FileRunner  (one file → its root group)
     └─ GroupRunner (recursive: hooks + children)
         ├─ GroupRunner (nested group, child TestCase)
         └─ ItemRunner  (single test closure)
```

- **SuiteRunner** shuffles the file keys before running, so order-dependent tests
  surface as flaky failures across runs.
- **GroupRunner** is the heart of the engine: it runs `beforeAll` once, then for
  each node runs `beforeEach` → node → `afterEach`, then `afterAll` once. Nested
  groups receive a freshly-opened child `TestCase` (§5).
- **ItemRunner** binds the test closure to the `TestCase` and executes it inside a
  `try/catch (\Throwable)`. A failed assertion throws `AssertionError`; any other
  exception also counts the test as failed.
- **HookRunner** binds and runs hook closures; a hook exception is re-thrown as a
  `RunnerException` so it bubbles up as a group failure.

### 3.3 Checks — assertions & expectations

Two interchangeable assertion styles are provided:

| Fluent (Pest-style) | Direct (`$this->`) | Semantics |
|---|---|---|
| `expect($a)->toBe($b)` | `$this->assertSame($b, $a)` | strict `===` |
| `expect($a)->toBeBetween($min,$max)` | `$this->assertBetween($a,$min,$max)` | inclusive range |
| `expect($a)->toBeEmpty()` | `$this->assertEmpty($a)` | PHP `empty()` |
| `expect($a)->toBeTrue()` | `$this->assertTrue($a)` | strict `=== true` |
| `expect($a)->toBeTruthy()` | `$this->assertTruthy($a)` | truthy `!$a` |

Every assert also has a negated twin (`assertNotSame`, `assertNotTrue`, …), and the
fluent API supports inversion via `expect($a)->not->toBe($b)`.

**`Assertions` is a trait** mixed into `TestCase`, so both `$this`-style calls and
the fluent API resolve to the same code path. Internally they funnel into a private
`process()`, which:

- on success prints `assertion is ok (file:line)` and increments `TestStats::incOkAsserts()`;
- on failure increments `incErrAsserts()` and **throws `AssertionError`**, which
  `ItemRunner` catches and counts as a failed test.

**`Expectation`** is a tiny value wrapper. It holds the value and an `inverted`
flag. Accessing `->not` (via `__get`) returns an inverted copy of the expectation.
Each `toBe*` method delegates to the current `TestCase` obtained through
`probatio()->runner()->getCurrentCase()`.

### 3.4 Suite / state objects

- **`TestSuite`** — a singleton (`getInstance()`), the only global mutable hub.
  Owns `Config`, `TestRegistry`, `TestStats`, `SuiteRunner`, plus parsed CLI args.
- **`TestRegistry`** — registration facade. `registerTestFile()` creates a
  `TestFile`, `require_once`s it, and stores it keyed by path. The API functions
  (`describe`, `test`, …) resolve their target group through
  `getCurrentFile() → getCurrentGroup()`.
- **`TestStats`** — plain counters (`okTests`, `errTests`, `okAsserts`,
  `errAsserts`).
- **`Config`** — feed from environment (see §7).

The namespaced functions (`Probatio\Functions\describe`, …) are the canonical API
surface; `GlobalFunctions.php` only mirrors them into the global namespace when
`PROBATIO_REGISTER_GLOBALS` is enabled and the names are still free.

---

## 4. Registration phase in detail

`TestRegistry::registerTestFile($path)`:

```php
$this->currentFile = new TestFile($path);
require_once $path;                 // test file body executes
$this->testFiles[$path] = $this->currentFile;
$this->currentFile = null;
```

While the file is being required, calls like `describe('calc', fn …)` recurse:

```
describe() → TestSuite::registerGroup()
           → TestRegistry::registerGroup()
           → TestFile::registerGroup()
               └─ currentGroup->addNestedGroup()   # new leaf node
                  currentGroup = newGroup          # descend
                  $fun()                           # run describe body NOW
                  currentGroup = oldGroup          # re-ascend
```

Note that the **describe closure is invoked synchronously during registration** to
populate its children. Because registration is depth-first, the tree is guaranteed
complete before any execution begins. `context()` is an alias of `describe()`.

Hooks and items likewise attach to `currentGroup` at call time, so their placement
inside a `describe` body determines their scope. File-level `test()`/`beforeEach()`
calls (outside any `describe`) land on the lazily-created root group.

---

## 5. Execution phase & TestCase lifecycle

### 5.1 TestCase creation

A `TestCase` is per *test*, not per file. Two relevant places instantiate it:

- `SuiteRunner::run()` creates one `TestCase` passed to each `FileRunner`.
- `GroupRunner::run()` creates a **child** `TestCase` (`new TestCase($tc)`) for each
  nested group, chaining back to the parent.

`SuiteRunner` tracks `currentCase`/`currentFile`; `Expectation` uses
`getCurrentCase()` to find the active context when assertions are made outside a
bound `$this` scope.

### 5.2 Closure binding

Test and hook closures are rebound to the active `TestCase` via
`Closure::bindTo($tc, $tc)`, so inside a closure:

- `$this` is the `TestCase`;
- `$this->assertSame(...)`, `expect(...)`, `$this->get()/set()/unset()` all work.

### 5.3 State sharing

`TestCase` is also a tiny key-value store (`assigns` array). Shared setup/teardown
is expressed with hooks:

```php
beforeAll(fn () => $this->set('calc', new Calculator()));
test('sum', fn () => expect($this->get('calc')->sum(1, 2))->toBe(3));
```

Because nested groups get a *child* `TestCase`, values set on a parent are not
inherited — `get()` reads only `$this->assigns`. State is intentionally scoped to a
single group + its hooks, and vanishes between groups (each item is also not
guaranteed the same instance across groups).

### 5.4 Hook ordering

For a group, the effective order is:

```
beforeAll
for each node:
    beforeEach
    node                                   # nested group → recursive GroupRunner
    afterEach
afterAll
```

Notes:
- `beforeEach`/`afterEach` run even for nested-group nodes, and the nested group
  then runs its own hooks on its own child `TestCase`.
- There is deliberately **no isolation** between items by default: files are
  shuffled at the suite level, but items within a group run in declaration order.

---

## 6. Location tracking (error reporting)

Probatio reports precise `file:line` on the terminal:

- `Location::fromFun(Closure)` — uses `ReflectionFunction` on the closure.
- `Location::fromCaller()` — for assertions, inspects the backtrace *above the
  framework* via `Caller`, which skips `Caller::EXCLUDE_FILES` (all framework
  internals) to find the user's test file line.
- `Location::fromException(Throwable)` — derives location from the thrown error.

`Path::maybeRemoveCwd()` strips the CWD prefix so messages show relative paths.

---

## 7. Configuration

All configuration flows through environment variables (`Suite\Config` + `Utils\Env`):

| Variable | Default | Purpose |
|---|---|---|
| `PROBATIO_TESTS_DIR` | `tests` | directory scanned recursively for test files |
| `PROBATIO_MAIN_FILE` | `{tests_dir}/tests.php` | optional bootstrap required first |
| `PROBATIO_REGISTER_GLOBALS` | `true` | register `describe`/`test`/… globally |

### Test file discovery (`Utils\Path`)

`getFilesRecursive()` walks the tree with `RecursiveDirectoryIterator`; a file counts
as a test file when it matches `/(\w+(_test|Test)(s?))\.php$/`. If invalid file
arguments are passed on the CLI, they are silently ignored (only paths matching the
same pattern are collected).

### Exit codes

`TestSuite::run()` exits non-zero (1) whenever any test or assertion failed, which
works natively with CI pipelines. A suite run with zero registered files still
succeeds, so "no tests discovered" is not an error by design.

---

## 8. Output (`Utils\Printer`)

`Printer` renders a hierarchical, indented report:

- level-based indentation (`incLevel`/`decLevel`/`resetLevel`) mirrors the
  group nesting;
- colored prefixes / emojis distinguish files, groups, items, pass/fail
  (`noticeFile`, `noticeGroup`, `noticeItem`, `noticeOk`, `noticeErr`);
- the final `Summary:` line aggregates `tests:` and `asserts:` from `TestStats`
  and is printed in green (success) or red (failure).

---

## 9. Self-testing & CI

The framework tests itself: the `examples/tests` directory is the integration test
suite, run via `./bin/probatio` with `PROBATIO_TESTS_DIR=examples/tests`. A separate
`examples/buggy_tests` fixture intentionally fails (floating-point precision) and
`scripts/run-buggy-tests.sh` asserts the runner reports failure and exits `1`.
GitHub Actions runs the same flow on PHP 7.2 (see `.github/workflows/tests.yml`).

---

## 10. Design decisions & trade-offs

1. **Deep-copy two-phase model over `eval`-style registration.** Trees are built
   completely before execution; runners are stateless-ish, which keeps execution
   simple and side-effect checkable.
2. **Zero dependencies.** Even string helpers (`Str`) are polyfilled; only Composer
   itself and `php-cs-fixer` (dev) are involved.
3. **PHP 7.2 floor with forward-looking polyfills** (`str_starts_with`, etc.),
   while CI validates newer PHP implicitly.
4. **Shuffled file order** trades reproducibility for detection of
   inter-file state leaks.
5. **`$this`-bound closures + trait-based assertions** give two API styles while
   reusing a single code path.
6. **Singleton `TestSuite`** keeps the API global-state free for writers (functions),
   at the cost of a single mutable hub — acceptable for a CLI test runner.
7. **`describe()` bodies execute synchronously at registration**, so a heavy block
   of declarations runs during discovery; a deliberate simplification.

## 11. Extension points / roadmap

- Planned compat layers (Pest / PHPUnit) would map onto the `Definitions` +
  `Checks` seams (`Definition`, `Runnable`, `Assertions`).
- A coverage reporter would hook `TestStats`/`Printer` without touching the runner
  chain.
- Test isolation / sandboxing could be added inside `GroupRunner`/`ItemRunner`.
