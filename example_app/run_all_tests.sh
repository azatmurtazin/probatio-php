#!/bin/bash

output=$(php ./tests/tests.php)
exit_code=$?
last_line=$(echo "$output" | tail -n 1)
search_string="some tests failed"

if [ $exit_code -eq 1 ] && [[ "$last_line" == *"$search_string"* ]]; then
    echo "$output"
    echo ""
    echo "Condition met: exit code is 1, and the last line contains '$search_string'."
else
    echo "Condition NOT met."
    echo "Received exit code: $exit_code"
    echo "Last line of output: $last_line"
fi
