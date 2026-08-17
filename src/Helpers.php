<?php

declare(strict_types=1);

namespace Probatio\Helpers;

use Probatio\TestCase;
use Probatio\TestSuite;
use Probatio\Utils;


function test(?string $name, callable $fun)
{
    $caller = Utils::getCaller();
    $ts = TestSuite::getInstance();
    $fun = function (TestCase $tc) use ($name, $fun, $caller) {
        $tc->test($name, $fun, $caller);
    };
    $ts->describe(null, $fun, $caller);
}
