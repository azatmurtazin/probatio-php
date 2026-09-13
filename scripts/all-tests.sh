#!/usr/bin/env bash

for version in 72 74 80 82 85; do
  echo "🐘 test with php: $version"

  output=$(PHP_VERSION=$version just lib-tests 2>&1)
  exit_code=$?
  if [ $exit_code -eq 0 ]; then
    echo "  ✅ lib-tests are ok (php: $version)"
  else
    echo "$output"
    echo "❌ lib-tests failed (php: $version)"
    exit 1
  fi

  output=$(PHP_VERSION=$version just examples-tests 2>&1)
  exit_code=$?
  if [ $exit_code -eq 0 ]; then
    echo "  ✅ examples-tests are ok (php: $version)"
  else
    echo "$output"
    echo "❌ examples-tests failed (php: $version)"
    exit 1
  fi

  output=$(PHP_VERSION=$version just examples-buggy-tests 2>&1)
  exit_code=$?
  if [ $exit_code -eq 0 ]; then
    echo "  ✅ examples-buggy-tests are ok (php: $version)"
  else
    echo "$output"
    echo "❌ examples-buggy-tests failed (php: $version)"
    exit 1
  fi

  echo ""
done

echo "✅ All tests are ok!"
