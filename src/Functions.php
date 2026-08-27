<?php

declare(strict_types=1);

namespace Probatio\Functions;

use Probatio\Caller;
use Probatio\Expectation;
use Probatio\TestHook;
use Probatio\TestSuite;

function describe(?string $name, \Closure $fun)
{
    TestSuite::getInstance()->registerGroup($name, $fun, new Caller());
}

function context(?string $name, \Closure $fun)
{
    TestSuite::getInstance()->registerGroup($name, $fun, new Caller());
}

function test(?string $name, \Closure $fun)
{
    TestSuite::getInstance()->registerTestItem($name, $fun, new Caller());
}

function it(?string $name, \Closure $fun)
{
    $name = \sprintf("it %s", $name);
    TestSuite::getInstance()->registerTestItem($name, $fun, new Caller());
}

function beforeAll(\Closure $fun)
{
    $hook = new TestHook(TestHook::BEFORE_ALL, $fun, new Caller());
    TestSuite::getInstance()->registerHook($hook);
}

function afterAll(\Closure $fun)
{
    $hook = new TestHook(TestHook::AFTER_ALL, $fun, new Caller());
    TestSuite::getInstance()->registerHook($hook);
}

function beforeEach(\Closure $fun)
{
    $hook = new TestHook(TestHook::BEFORE_EACH, $fun, new Caller());
    TestSuite::getInstance()->registerHook($hook);
}

function afterEach(\Closure $fun)
{
    $hook = new TestHook(TestHook::AFTER_EACH, $fun, new Caller());
    TestSuite::getInstance()->registerHook($hook);
}

function expect($value): Expectation
{
    return new Expectation($value);
}
