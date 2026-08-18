<?php

declare(strict_types=1);

require_once __DIR__."/../tests.php";

test("global test() and expect()", function() {
    $mul = function($a, $b) { return $a * $b; };
    $expected = 15;
    $actual = $mul(3, 5);
    expect($actual)->toBe($expected);
});

test("global assertEq()", function() {
    assertEq(12, 5 + 7);
    assertTrue(empty([]));
});
