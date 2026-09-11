![logo](/images/probatio-logo.png)

[![Tests](https://github.com/azatmurtazin/probatio-php/actions/workflows/tests.yml/badge.svg)](https://github.com/azatmurtazin/probatio-php)

---

**Probatio** is a lightweight, zero-dependency testing framework for PHP (>= 7.2).

It provides a Pest/RSpec/JS-like BDD API: `describe`, `context`, `test`, `it`, `expect`,
and lifecycle hooks (`beforeAll`, `afterAll`, `beforeEach`, `afterEach`).

## Installation

```bash
composer require --dev azatmurtazin/probatio-php
```

Or add it manually to `composer.json`:

```json
{
    "require-dev": {
        "azatmurtazin/probatio-php": "^X.Y"
    }
}
```

Then run `composer update`.

See the latest release on the [Packagist.org](https://packagist.org/packages/azatmurtazin/probatio-php)

## Basic usage

Create file `tests/Unit/MagicTest.php`:

```php
<?php
test('magic✨', function () {
    expect(join(["\xF0\x9F", "\xA6\x84"]))
        ->toBe('🦄');
});
```

Run all discovered tests (recursively from the tests directory, `tests/` by default):

```bash
./vendor/bin/probatio
```

Run a specific test file:

```bash
./vendor/bin/probatio tests/Unit/MagicTest.php
```

### Exit code

The process exits with `0` when every test and assertion passed, and `1` when any
test failed or any assertion throw. This makes it CI-friendly out of the box.

## See also

* [Code examples](/examples/)
* [Detailed usage](/docs/usage.md)
* [Architecture](/docs/architecture.md)
* [Changelog](/docs/changelog.md)
* [TODO](/docs/todo.md)

## License

It's open-sourced software licensed under the [MIT license](/LICENSE).
