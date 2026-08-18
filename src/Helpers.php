<?php

declare(strict_types=1);

namespace Probatio\Helpers;

use Probatio\BareAssertion;
use Probatio\Caller;
use Probatio\Expectation;
use Probatio\TestCase;
use Probatio\TestSuite;

function test(?string $name, \Closure $fun)
{
    $caller = new Caller();
    $ts = TestSuite::getInstance();
    $fun = function (TestCase $tc) use ($name, $fun, $caller) {
        $tc->test($name, $fun, $caller);
    };
    $ts->describe(null, $fun, $caller);
}

function assertEq($expected, $actual)
{
    return (new BareAssertion())->assertEq($expected, $actual);
}

function assertTrue($value)
{
    return (new BareAssertion())->assertTrue($value);
}

function expect($value): Expectation
{
    return new Expectation($value);
}
