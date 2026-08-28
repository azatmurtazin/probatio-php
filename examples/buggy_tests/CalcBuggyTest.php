<?php

use Probatio\Examples\Calculator;

test("floating-point rounding error", function() {
    $a = 0.1;
    $b = 0.2;
    $c = 0.3;

    expect(Calculator::add($a, $b))->toBe($c);
});
