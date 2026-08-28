list:
  @just --list

probatio_bin := "./bin/probatio"

# Examples: all tests
examples-all-tests:
  PROBATIO_MAIN_FILE="examples/tests/tests.php" {{probatio_bin}}

# Examples: greeter test
examples-greeter-test:
  {{probatio_bin}} examples/tests/Unit/GreeterTest.php

# Examples: calculator test
examples-calc-test:
  {{probatio_bin}} examples/tests/Unit/CalculatorTest.php
