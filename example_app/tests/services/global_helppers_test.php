<?php

declare(strict_types=1);

require_once __DIR__."/../tests.php";

use Probatio\TestCase;

test("global helpers: 3 * 5", function(TestCase $tc) {
    $mul = function($a, $b) { return $a * $b; };
    $expected = 15;
    $actual = $mul(3, 5);
    $tc->assertEq($expected, $actual);
});
