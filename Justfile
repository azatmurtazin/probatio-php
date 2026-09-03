list:
  @just --list

PHP_VERSION_7_2 := "7.2.34"
PHP_VERSION_8_5 := "8.5.9"
PHP_VERSION := PHP_VERSION_7_2

CS_FIXER_VERSION := "3.95.24"
CS_FIXER_LINK := "https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v"+CS_FIXER_VERSION+"/php-cs-fixer.phar"

export PROBATIO_TESTS_DIR := "examples/tests"

PROBATIO_BIN := "./bin/probatio"
PHP_BIN := "ASDF_PHP_VERSION="+PHP_VERSION+" php"
NEW_PHP_BIN := "ASDF_PHP_VERSION="+PHP_VERSION_8_5+" php"

# Print old PHP version
old-php-version:
  {{PHP_BIN}} --version

# Print new PHP version
new-php-version:
  {{NEW_PHP_BIN}} --version

# Ensure php-cs-fixer is downloaded
get-cs-fixer:
    #!/usr/bin/env bash
    if [ ! -f "php-cs-fixer.phar" ]; then \
        echo "Downloading php-cs-fixer.phar..."; \
        curl -L "{{CS_FIXER_LINK}}" -o php-cs-fixer.phar; \
        chmod +x php-cs-fixer.phar; \
    else \
        echo "php-cs-fixer.phar already exists."; \
    fi

# Format source code
format: get-cs-fixer
  {{NEW_PHP_BIN}} ./php-cs-fixer.phar fix

# Examples: all tests
examples-all-tests:
   {{PROBATIO_BIN}}

# Examples: greeter test
examples-greeter-test:
  {{PROBATIO_BIN}} examples/tests/Unit/GreeterTest.php

# Examples: calculator test
examples-calc-test:
  {{PROBATIO_BIN}} examples/tests/Unit/CalculatorTest.php

examples-buggy-tests:
  ./scripts/run-buggy-tests.sh
