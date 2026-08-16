# List available commands
list:
  just --list

example_app_dir := "example_app"

# Example app: run tests
[working-directory(example_app_dir)]
example-app-tests:
  php tests/tests.php

# Example app: install deps
[working-directory(example_app_dir)]
example-app-install:
  composer install
