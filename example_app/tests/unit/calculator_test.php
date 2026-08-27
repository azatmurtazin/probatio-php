<?php

declare(strict_types=1);

require_once __DIR__ . "/../tests.php";

use ExampleApp\Calculator;

describe("tests of add and sub", function() {
    test("add", function() {
        $expected = 12;
        $actual = Calculator::add(5, 7);
        $this->assertEq($expected, $actual);
    });

    test("sub", function() {
        $expected = 2;
        $actual = Calculator::sub(7, 5);
        $this->assertEq($expected, $actual);
    });
});

describe("tests of mul and div", function() {
    test("mul", function() {
        $expected = 21;
        $actual = Calculator::mul(3, 7);
        $this->assertEq($expected, $actual);
    });

    test("ok div", function() {
        $expected = 1.4;
        $actual = Calculator::div(7, 5);
        $this->assertEq($expected, $actual);
    });

    test("div to zero", function() {
        $expected = INF;
        $actual = Calculator::div(42, 0);
        $this->assertEq($expected, $actual);
    });
});
