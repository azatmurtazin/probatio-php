list:
  @just --list

probatio_bin := "./bin/probatio"

# Examples: all tests
examples-all-tests:
  {{probatio_bin}} --tests-dir=examples/tests

# Examples: greeter test
examples-greeter-test:
  {{probatio_bin}} examples/tests/Unit/GreeterTest.php

# Examples: calculator test
examples-calc-test:
  {{probatio_bin}} examples/tests/Unit/CalculatorTest.php
