# Usage

Probatio is a lightweight, zero-dependency testing framework for PHP (>= 7.2).
It provides a Pest/JS-like BDD API: `describe`, `context`, `test`, `it`, `expect`,
and lifecycle hooks (`beforeAll`, `afterAll`, `beforeEach`, `afterEach`).

---

## Installation

```bash
composer require --dev azatmurtazin/probatio-php
```

Or add it manually to `composer.json`:

```json
{
    "require-dev": {
        "azatmurtazin/probatio-php": "^0.1"
    }
}
```

Then run `composer update`.

---

## Running tests

Run all discovered tests (recursively from the tests directory, `tests/` by default):

```bash
./vendor/bin/probatio
```

Run a specific test file:

```bash
./vendor/bin/probatio tests/Unit/MagicTest.php
```

Run several specific files:

```bash
./vendor/bin/probatio tests/Unit/CalcTest.php tests/Unit/GreeterTest.php
```

### Exit code

The process exits with `0` when every test and assertion passed, and `1` when any
test failed or any assertion throw. This makes it CI-friendly out of the box.

---

## Writing tests

A test file is an ordinary PHP script that calls the global API functions.
**No class is required** — just functions.

### `test()` and `it()`

```php
test('sum', function () {
    expect(Calculator::sum(1, 2))->toBe(3);
});

it('performs sums', function () {
    expect(Calculator::sum(1, 2))->toBe(3);
});
```

`test()` and `it()` are interchangeable. `it()` simply prefixes the name with
`it `, so `it('performs sums')` is rendered as `it performs sums`.

### `describe()` and `context()`

Group related tests. Both are aliases:

```php
describe('sum', function () {
    it('may sum integers', function () {
        expect(Calculator::sum(1, 2))->toBe(3);
    });

    it('may sum floats', function () {
        expect(Calculator::sum(1.5, 2.5))->toBe(4.0);
    });
});
```

`describe`/`context` blocks can be **nested arbitrarily**. Test files can also mix
file-level `test()` calls and `describe()` groups in the same file.

---

## Assertions

Two equivalent styles are available; mix them freely.

### Fluent expectations

```php
expect($actual)->toBe($expected);
```

The `expect()` API is chainable and supports a `not` modifier:

```php
expect(1)->toBe(1);
expect('1')->not->toBe(1);
expect(new StdClass())->not->toBe(new StdClass());   // not the same instance
```

Available matchers:

| Matcher | Passes when |
|---|---|
| `->toBe($expected)` | value is strictly identical (`===`) to `$expected` |
| `->toBeBetween($min, $max)` | value is in the inclusive range `[$min, $max]` |
| `->toBeEmpty()` | value is empty per PHP `empty()` |
| `->toBeTrue()` | value is strictly `=== true` |
| `->toBeTruthy()` | value is truthy |
| `->toBeFalse()` | value is strictly `=== false` |
| `->toBeFalsy()` | value is falsy |

Every matcher has a `->not->` negated form.

```php
test('toBe', function () {
    expect(1)->toBe(1);
    expect('1')->not->toBe(1);
    expect(new StdClass())->not->toBe(new StdClass());
});

test('toBeBetween', function () {
    expect(2)->toBeBetween(1, 3);
    expect(1.5)->toBeBetween(1, 2);

    $date = new DateTime('2026-08-22');
    expect($date)->toBeBetween(
        new DateTime('2026-08-21'),
        new DateTime('2026-08-23')
    );
});

test('toBeEmpty', function () {
    expect('')->toBeEmpty();
    expect([])->toBeEmpty();
    expect(null)->toBeEmpty();
    expect('false')->not->toBeEmpty();
});
```

### Direct assertions via `$this`

Inside any test or hook closure, `$this` is a `TestCase`, which exposes
PHPUnit-style methods:

```php
test('sum via assert', function () {
    $this->assertSame(3, Calculator::sum(1, 2));
});
```

Available methods:

| Method | Check |
|---|---|
| `assertSame($expected, $actual)` / `assertNotSame(...)` | strict equality |
| `assertEquals($expected, $actual)` | loose equality (`==`) |
| `assertTrue` / `assertNotTrue` | strict `=== true` |
| `assertFalse` / `assertNotFalse` | strict `=== false` |
| `assertTruthy` / `assertNotTruthy` | truthy |
| `assertFalsy` / `assertNotFalsy` | falsy |
| `assertBetween($actual, $min, $max)` / `assertNotBetween(...)` | inclusive range |
| `assertEmpty($actual)` / `assertNotEmpty(...)` | empty / not empty |

---

## Lifecycle hooks

Four hooks are available inside a `describe`/`context` block (or at file level,
which applies to the file's root group):

| Hook | Runs |
|---|---|
| `beforeAll` | once, before all tests in the group |
| `afterAll` | once, after all tests in the group |
| `beforeEach` | before every test (and nested group) in the group |
| `afterEach` | after every test (and nested group) in the group |

```php
describe('Greeter', function () {
    beforeAll(function () {
        $this->set('greeter', new Greeter());
    });

    afterAll(function () {
        $this->unset('greeter');
    });

    test('with John', function () {
        $service = $this->get('greeter');
        expect($service->greet('John'))->toBe('Hello, John!');
    });
});
```

## Sharing state between tests

`TestCase` (the `$this` of your closures) is also a small key-value store:

- `$this->set($key, $value)`
- `$this->get($key)`
- `$this->unset($key)`

Use `beforeAll`/`beforeEach` to prepare shared fixtures and `afterAll`/`afterEach`
to tear them down. State is scoped to the current group: nested `describe` blocks
receive their own child `TestCase`, so values set on a parent are not visible inside
a nested group (and vice versa).

---

## Test file discovery

By default Probatio scans the `tests` directory **recursively**. A file is treated
as a test file only when its name matches `/(\w+(_test|Test)(s?))\.php$/`, for
example `CalcTest.php`, `calc_test.php`, or `MagicTests.php`.

An optional bootstrap file (`tests/tests.php` by default) is required *before* the
suite scan when it exists. Use it for global setup, auto-imports, or helpers.

---

## Configuration

Probatio is configured through environment variables:

| Variable | Default | Purpose |
|---|---|---|
| `PROBATIO_TESTS_DIR` | `tests` | directory scanned recursively for test files |
| `PROBATIO_MAIN_FILE` | `{tests_dir}/tests.php` | optional bootstrap file required first |
| `PROBATIO_REGISTER_GLOBALS` | `true` | register `describe`/`test`/`expect`/… in the global namespace |

Example:

```bash
PROBATIO_TESTS_DIR=tests/Unit ./vendor/bin/probatio
```

> If `PROBATIO_REGISTER_GLOBALS` is disabled, call the full API through the
> `Probatio\Functions` namespace instead:
> `Probatio\Functions\test('x', fn () => ...)`.

---

## Understanding output

```
Probatio: 0.1.1
PHP version: 7.2.34

run all tests

run file examples/tests/Unit/BasicTests.php
  test sum
    assertion is ok (examples/tests/Unit/BasicTests.php:10)
    test 'sum' is ok
  ...
Summary:
  tests: [12 / 12] - ok;
asserts: [20 / 20] - ok
```

A failing run prints the exception class/message/location for each failed test and
a red `Summary:` line, then the runner exits with code `1`:

```
  test div to zero
    AssertionError: INF is not identical to 0.2 (examples/tests/Unit/CalcBuggyTest.php:xx)
    test 'div to zero' failed
Summary:
  tests: [3 / 5] - 2 failed;
asserts: [3 / 4] - 1 failed
```

---

## HTTPS / helpers note

Probatio has **zero runtime dependencies** — nothing to configure besides PHP.
Test files only need access to the framework's global functions (auto-registered)
and your own classes, loaded e.g. via Composer's autoloader (`use` statements).

---

## Examples

Run the framework's own example suite:

```bash
PROBATIO_TESTS_DIR=examples/tests ./bin/probatio
```

Or with the project's task runner (`just`):

```bash
just examples-all-tests      # all examples
just examples-greeter-test   # only Examples/Unit/GreeterTest.php
just examples-buggy-tests    # intentionally failing fixtures → verifies exit code 1
just tests                   # everything
```

See [`/examples`](/examples) for complete runnable files.
