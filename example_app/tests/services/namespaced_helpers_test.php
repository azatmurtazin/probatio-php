<?php

declare(strict_types=1);

require_once __DIR__."/../tests.php";

use Probatio\TestCase;
use function Probatio\Helpers\test;

test("namespaced helpers: 5 + 7", function(TestCase $tc) {
    $add = function($a, $b) { return $a + $b; };
    $expected = 12;
    $actual = $add(5, 7);
    $tc->assertEq($expected, $actual);
});
