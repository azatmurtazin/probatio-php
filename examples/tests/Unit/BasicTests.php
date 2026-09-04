<?php

declare(strict_types=1);

use Probatio\Examples\Calculator;

test('sum', function () {
    $result = Calculator::sum(1, 2);

    expect($result)->toBe(3);
});

it('performs sums', function () {
    $result = Calculator::sum(1, 2);

    expect($result)->toBe(3);
});

describe('sum', function () {
    it('may sum integers', function () {
        $result = Calculator::sum(1, 2);

        expect($result)->toBe(3);
    });

    it('may sum floats', function () {
        $result = Calculator::sum(1.5, 2.5);

        expect($result)->toBe(4.0);
    });
});

test('sum via assert', function () {
    $result = Calculator::sum(1, 2);

    // Same as expect($result)->toBe(3)
    $this->assertSame(3, $result);
});
