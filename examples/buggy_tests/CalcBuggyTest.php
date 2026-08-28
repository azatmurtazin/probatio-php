<?php

use Probatio\Examples\Calculator;

test("floating-point rounding error", function() {
    $a = 0.1;
    $b = 0.2;
    $c = 0.3;

    expect(Calculator::add($a, $b))->toBe($c);
});

test("valid floating-point", function() {
    $a = 0.1;
    $b = 0.2;
    $c = 0.3;
    $e = 0.0000001;
    $result = abs(Calculator::add($a, $b) - $c) < $e;

    expect($result)->toBe(true);
});
