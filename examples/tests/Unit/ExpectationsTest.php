<?php

declare(strict_types=1);

// This expectation ensures that both $value and $expected share the same type and value.
// If used with objects, it ensures that both variables refer to the exact same object:
test('toBe', function () {
    expect(1)->toBe(1);
    expect('1')->not->toBe(1);
    expect(new StdClass())->not->toBe(new StdClass());
});

// This expectation ensures that $value is between two values. It works with int, float, and DateTime:
test('toBeBetween', function () {
    expect(2)->toBeBetween(1, 3);
    expect(1.5)->toBeBetween(1, 2);
    expect(10)->not->toBeBetween(18, INF);

    $expectationDate = new DateTime('2026-08-22');
    $oldestDate = new DateTime('2026-08-21');
    $latestDate = new DateTime('2026-08-23');

    expect($expectationDate)->toBeBetween($oldestDate, $latestDate);
});

// This expectation ensures that $value is empty:
test('toBeEmpty', function () {
    expect('')->toBeEmpty();
    expect([])->toBeEmpty();
    expect(null)->toBeEmpty();
    expect(0)->toBeEmpty();
    expect(0.0)->toBeEmpty();
    expect(false)->toBeEmpty();
    expect('false')->not->toBeEmpty();
});
