#!/bin/bash

output=$(PROBATIO_TESTS_DIR="./examples/buggy_tests" ./bin/probatio)
exit_code=$?
last_line=$(echo "$output" | tail -n 1)
search_string="failed"

echo "$output"
echo ""

if [ $exit_code -eq 1 ] && [[ "$last_line" == *"$search_string"* ]]; then
    echo "Condition met: exit code is 1, and the last line contains '$search_string'."
else
    echo "Condition NOT met."
    echo "Received exit code: $exit_code"
    echo "Last line of output: $last_line"
fi
