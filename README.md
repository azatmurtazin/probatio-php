# Probatio-php

![logo](/images/probatio-logo.png)

It's a simple testing framework. Compatible with PHP >= 7.2. Zero additional dependencies.

## Installation

```bash
composer require --dev azatmurtazin/probatio-php
```

Or add to the `composer.json`

```json
{
    "require-dev": {
        "azatmurtazin/probatio-php": "^0.1"
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

## License

[MIT](/LICENSE)
