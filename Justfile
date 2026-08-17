# List available commands
list:
  just --list

example_app_dir := "example_app"

# Example app: install deps
[working-directory(example_app_dir)]
example-app-install:
  composer install

# Example app: run tests
[working-directory(example_app_dir)]
example-app-tests:
  ./run_all_tests.sh

# Example app: calculator tests
[working-directory(example_app_dir)]
example-app-calc-tests:
  php tests/services/calculator_test.php
