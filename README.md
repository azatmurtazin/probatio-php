![logo](/images/probatio-logo.png)

[![Tests](https://github.com/azatmurtazin/probatio-php/actions/workflows/tests.yml/badge.svg)](https://github.com/azatmurtazin/probatio-php)

---

**Probatio-php** is a simple testing framework. Compatible with PHP >= 7.2 / 8. Zero additional dependencies.

## Installation

```bash
composer require --dev azatmurtazin/probatio-php
```

Or add dependency manually to the `composer.json`,
see the latest release on the [Packagist.org](https://packagist.org/packages/azatmurtazin/probatio-php)

```json
{
    "require-dev": {
        "azatmurtazin/probatio-php": "^X.Y"
    }
}
```

And run `composer update`

## Basic usage

Create file `tests/Unit/MagicTest.php`:

```php
<?php
test('magic✨', function () {
    expect(join(["\xF0\x9F", "\xA6\x84"]))
        ->toBe('🦄');
});
```

And run:

```bash
./vendor/bin/probatio tests/Unit/MagicTest.php
```

## Examples

See `examples` directory for more usage examples.

## TODO

* ✅ Basic functionality
* ✅ Pre-commit hooks
* ✅ Publish to the Packagist
* ✅ Github actions
* 🛠️ Pest compatibility
  * 🛠️ Expectation
  * ⏳ Other features
* ⏳ More examples and docs
* ⏳ PHPUnit compatibility
* ⏳ Code coverage reports

## License

It's open-sourced software licensed under the [MIT license](/LICENSE).
