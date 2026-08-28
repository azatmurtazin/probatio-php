list:
  @just --list

PROBATIO_BIN := "./bin/probatio"
export PROBATIO_MAIN_FILE := "examples/tests/tests.php"

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
