<?php

declare(strict_types=1);

use Probatio\Examples\Calculator;

describe('tests of sum and sub', function () {
    test('sum', function () {
        $expected = 12;
        $actual = Calculator::sum(5, 7);
        expect($actual)->toBe($expected);
    });

    test('sub', function () {
        $expected = 2;
        $actual = Calculator::sub(7, 5);
        expect($actual)->toBe($expected);
    });
});

describe('tests of mul and div', function () {
    test('mul', function () {
        $expected = 21;
        $actual = Calculator::mul(3, 7);
        expect($actual)->toBe($expected);
    });

    test('ok div', function () {
        $expected = 1.4;
        $actual = Calculator::div(7, 5);
        expect($actual)->toBe($expected);
    });

    test('div to zero', function () {
        $expected = INF;
        $actual = Calculator::div(42, 0);
        expect($actual)->toBe($expected);
    });
});
