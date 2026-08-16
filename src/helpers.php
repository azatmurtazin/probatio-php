<?php

declare(strict_types=1);

use Probatio\TestCase;
use Probatio\TestSuite;
use Probatio\Utils;

if (!function_exists("test")) {
    function test(?string $name = null, callable $fun)
    {
        $caller = Utils::getCaller();
        $ts = TestSuite::getInstance();
        $fun = function (TestCase $tc) use ($name, $fun, $caller) {
            $tc->test($name, $fun, $caller);
        };
        $ts->describe(null, $fun, $caller);
    }
} else {
    throw new \RuntimeException("helper test() is already defined");
}
