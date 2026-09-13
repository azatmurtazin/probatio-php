list:
  @just --list

CS_FIXER_VERSION := "3.95.24"
CS_FIXER_LINK := "https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v"+CS_FIXER_VERSION+"/php-cs-fixer.phar"

# Available version: 72, 74, 80, 82 and 85
PHP_VERSION := env("PHP_VERSION", "85")
PHP_SERVICE := "php"+PHP_VERSION
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
  @just docker-php ./php-cs-fixer.phar fix

# Examples: all tests
examples-all-tests:
  @PROBATIO_TESTS_DIR=examples/tests just docker-php ./bin/probatio

# Examples: greeter test
examples-greeter-test:
  @PROBATIO_TESTS_DIR=examples/tests just docker-php ./bin/probatio examples/tests/Unit/GreeterTest.php

# Examples: run buggy tests
examples-buggy-tests:
  @./scripts/run-buggy-tests.sh

# Run all tests
tests: examples-all-tests examples-buggy-tests
  @echo "\n✅ All tests are ok!"

docker-php *args="":
  {{DCR}} --rm {{DOCKER_ENV_VARS}} {{PHP_SERVICE}} php {{args}}
