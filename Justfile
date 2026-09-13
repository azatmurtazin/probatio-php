list:
  @just --list

CS_FIXER_VERSION := "3.95.24"
CS_FIXER_LINK := "https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v"+CS_FIXER_VERSION+"/php-cs-fixer.phar"

PHP_VERSION := "72"
DCR := "docker compose run"
DOCKER_ENV_VARS := "-e PROBATIO_TESTS_DIR -e PROBATIO_MAIN_FILE -e PROBATIO_REGISTER_GLOBALS"

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
  @just php74 ./php-cs-fixer.phar fix

# Examples: all tests
examples-all-tests:
  PROBATIO_TESTS_DIR=examples/tests just php{{PHP_VERSION}} ./bin/probatio

# Examples: greeter test
examples-greeter-test:
  @just php{{PHP_VERSION}} ./bin/probatio examples/tests/Unit/GreeterTest.php

# Examples: run buggy tests
examples-buggy-tests:
  ./scripts/run-buggy-tests.sh

# Run all tests
tests: examples-all-tests examples-buggy-tests
  @echo "\n✅ All tests are ok!"

# Run scripts with PHP version, ex.: just run php72 ./bin/probatio
run service *args:
    {{DCR}} --rm {{service}} php {{args}}

# Run with PHP 7.2
php72 *args="":
    @echo "### Running on PHP 7.2"
    {{DCR}} --rm {{ DOCKER_ENV_VARS }} php72 php {{args}}

# Run with PHP 7.4
php74 *args="":
    @echo "### Running on PHP 7.4"
    {{DCR}} --rm {{ DOCKER_ENV_VARS }} php74 php {{args}}

# Run with PHP 8.0
php80 *args="":
    @echo "### Running on PHP 8.0"
    {{DCR}} --rm {{ DOCKER_ENV_VARS }} php80 php {{args}}

# Run with PHP 8.2
php82 *args="":
    @echo "### Running on PHP 8.2"
    {{DCR}} --rm {{ DOCKER_ENV_VARS }} php82 php {{args}}

# Run with PHP 8.5
php85 *args="":
    @echo "### Running on PHP 8.5"
    {{DCR}} --rm {{ DOCKER_ENV_VARS }} php85 php {{args}}
