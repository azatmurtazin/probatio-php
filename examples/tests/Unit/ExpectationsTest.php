<?php

declare(strict_types=1);

// This expectation ensures that both $value and $expected share the same type and value.
// If used with objects, it ensures that both variables refer to the exact same object:
test('toBe', function () {
    expect(1)->toBe(1);
    expect('1')->not->toBe(1);
    expect(new StdClass())->not->toBe(new StdClass());
});
