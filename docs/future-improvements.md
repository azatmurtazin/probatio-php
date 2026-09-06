# Future Improvements

Suggested improvements to Probatio, roughly ordered by impact.

## High impact

### 1. Unit tests for the framework itself

Right now the project only validates itself via `examples/tests/` (happy path) and `examples/buggy_tests/` (failure detection). There is no coverage of the API surface itself: `expect()->not->toBeBetween`, hook ordering, nested-group state scoping, `it()` name prefixing, etc.

Consider adding a dedicated `tests/` directory for the `src/` tree, using Probatio as the harness. This catches regressions and documents expected behavior at the same time.

### 2. Empty-suite behavior

`TestSuite::run()` returns success (exit 0) when zero test files are discovered.
In CI, a misconfigured `PROBATIO_TESTS_DIR` or empty test directory silently passes.

**Idea:** warn (or fail) when no test files are registered, with an opt-in
`--allow-empty` flag for projects that intentionally have no tests yet.

---

## Medium impact

### 3. Preserve hook failure context

`HookRunner` wraps hook exceptions into a `RunnerException` with a new message,
discarding the original stack trace:

```php
// src/Probatio/Runners/HookRunner.php:28
throw new RunnerException("failed to run {$type} hook ($loc)");
```

**Idea:** pass `$e` as the `previous` exception so the original message and
stack are not lost in failure output.

### 4. Per-item TestCase isolation

`GroupRunner` reuses the **same** `TestCase` (and its `assigns` array) across all
items in a group. A test that mutates state via `$this->set()` can contaminate
sibling tests without any visible cause.

```php
// src/Probatio/Runners/GroupRunner.php:58
(new ItemRunner($node))->run($tc);
```

**Idea:** by default, clone `$tc` per item so each test gets a fresh `assigns`
scope while still inheriting values from parent groups via the parent chain.
Provide a flag (e.g. `describe('...', fn () => ..., isolate: false)`) to opt out
if needed.

### 5. CLI directory and glob support

File arguments on the CLI only accept files that pass `Path::isTestCaseFile()`.
Passing a directory (to run all tests inside it) or a glob (`tests/**/*Test.php`)
is silently ignored.

**Idea:** detect directories/glob patterns in CLI args, expand them through
`Path::getFilesRecursive()`, and merge with auto-discovered files.

---

## Lower impact / polish

### 6. Duplicate path guard

`TestRegistry::registerTestFile()` uses `require_once`, which silently
short-circuits a second include. However the file is still added to the
`$testFiles` map. Registering the same path twice would not fail but would
waste a key in the map and could produce confusing output.

**Idea:** skip (with a debug-level notice) files already present in the
registry.

### 7. Test runner affordances

Common test-runner features missing from Probatio:

- **`test.each` / data providers** — run the same test body with multiple inputs
- **`skip()` / `incomplete()`** — explicitly skip known issues
- **`TODO` / `expect()->todo()`** — mark known-failing tests without counting as errors

These are the features most likely to ease adoption for teams accustomed to
PHPUnit or Pest.

### 8. Documentation gaps

- `docs/architecture.md` and `docs/usage.md` should be cross-linked from the
  relevant source file docblocks

### 9. CI matrix across PHP versions

The `Justfile` defines `PHP_VERSION_8_5` (`Justfile:5`) but the GitHub Actions
workflow only tests on PHP 7.2 (`/.github/workflows/tests.yml`). Adding a
CI matrix covering at least 7.4, 8.0, 8.2, and 8.4 would catch version-specific
regressions (especially around `ReflectionFunction` behavior and string function
polyfills).

### 10. Composer scripts shortcut

Add `"scripts": { "test": "./bin/probatio tests" }` to `composer.json` so users
can run `composer test` without knowing the binary path, which lowers the barrier
for first-time contributors.
