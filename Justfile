list:
  @just --list

PHP_VERSION_7_2 := "7.2.34"
PHP_VERSION_8_5 := "8.5.9"
PHP_VERSION := PHP_VERSION_7_2

export PROBATIO_MAIN_FILE := "examples/tests/tests.php"

PROBATIO_BIN := "./bin/probatio"
PHP_BIN := "ASDF_PHP_VERSION="+PHP_VERSION+" php"
NEW_PHP_BIN := "ASDF_PHP_VERSION="+PHP_VERSION_8_5+" php"

# Print old PHP version
old-php-version:
  {{PHP_BIN}} --version

# Print new PHP version
new-php-version:
  {{NEW_PHP_BIN}} --version

# Format source code
format:
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
