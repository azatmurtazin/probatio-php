<?php

use Probatio\Examples\Calculator;
use Probatio\TestCase;

test("floating-point rounding error", function() {
    $a = 0.1;
    $b = 0.2;
    $c = 0.3;
    $result = Calculator::add($a, $b);

    expect(true)->toBe(true);

    /** @var TestCase */
    $that = $this;
    $that->assertEq($c, $result);
});

test("valid floating-point", function() {

    $a = 0.1;
    $b = 0.2;
    $c = 0.3;
    $e = 0.0000001;
    $result = abs(Calculator::add($a, $b) - $c) < $e;

    /** @var TestCase */
    $that = $this;
    $that->assertTrue($result);
});
