<?php

declare(strict_types=1);

use Probatio\Helpers;

if (!function_exists("test")) {
    function test(?string $name, \Closure $fun)
    {
        return Helpers\test($name, $fun);
    }
}

if (!function_exists("assertEq")) {
    function assertEq($expected, $actual)
    {
        return Helpers\assertEq($expected, $actual);
    }
}

if (!function_exists("assertTrue")) {
    function assertTrue($value)
    {
        return Helpers\assertTrue($value);
    }
}

if (!function_exists("expect")) {
    function expect($value)
    {
        return Helpers\expect($value);
    }
}
