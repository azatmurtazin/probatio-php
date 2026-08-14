<?php

declare(strict_types=1);

declare(strict_types=1);

require_once __DIR__."/../tests.php";

use Probatio\TestCase;
use Probatio\TestSuite;
use ExampleApp\Services\Calculator;

$ts = TestSuite::getInstance();

$ts->register("tests of add and sub", function(TestCase $tc) {
    $tc->test("add", function(TestCase $tc) {
        $expected = 12;
        $actual = Calculator::add(5, 7);
        $tc->assertEq($expected, $actual);
    });

    $tc->test("sub", function(TestCase $tc) {
        $expected = 2;
        $actual = Calculator::sub(7, 5);
        $tc->assertEq($expected, $actual);
    });
});

$ts->register("tests of mul and div", function(TestCase $tc) {
    $tc->test("mul", function(TestCase $tc) {
        $expected = 21;
        $actual = Calculator::mul(3, 7);
        $tc->assertEq($expected, $actual);
    });

    $tc->test("ok div", function(TestCase $tc) {
        $expected = 1.4;
        $actual = Calculator::div(7, 5);
        $tc->assertEq($expected, $actual);
    });

    $tc->test("div to zero", function(TestCase $tc) {
        $expected = INF;
        $actual = Calculator::div(42, 0);
        $tc->assertEq($expected, $actual);
    });
});
