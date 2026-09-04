<?php

use Probatio\Definitions\TestCase;
use Probatio\Examples\Calculator;

test('floating-point rounding error', function () {
    $a = 0.1;
    $b = 0.2;
    $c = 0.3;
    $result = Calculator::sum($a, $b);

    expect($c)->toBe($c);

    /** @var TestCase */
    $that = $this;
    $that->assertSame($c, $result);
});

test('valid floating-point', function () {

    $a = 0.1;
    $b = 0.2;
    $c = 0.3;
    $e = 0.0000001;
    $result = abs(Calculator::sum($a, $b) - $c) < $e;

    /** @var TestCase */
    $that = $this;
    $that->assertTrue($result);
});
