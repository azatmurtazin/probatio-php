<?php

declare(strict_types=1);

test("global test() and expect()", function() {
    $mul = function($a, $b) { return $a * $b; };
    $expected = 15;
    $actual = $mul(3, 5);
    expect($actual)->toBe($expected);
});

test("global assertEq()", function() {
    $this->assertEq(12, 5 + 7);
    $this->assertTrue(empty([]));
});
