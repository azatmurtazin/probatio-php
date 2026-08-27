<?php

declare(strict_types=1);

use ExampleApp\Calculator;
use function Probatio\Functions\test;

test("namespaced functions: 5 + 7", function() {
    $expected = 12;
    $actual = Calculator::add(5, 7);
    $this->assertEq($expected, $actual);
});
