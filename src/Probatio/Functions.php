<?php

declare(strict_types=1);

namespace Probatio\Functions;

use Probatio\Checks\Expectation;
use Probatio\Definitions\TestHook;
use Probatio\Suite\TestSuite;

/**
 * probatio()
 * @return TestSuite
 */
function probatio(): TestSuite
{
    return TestSuite::getInstance();
}

/**
 * describe()
 * @param ?string $name
 * @param \Closure $fun
 * @return void
 */
function describe(?string $name, \Closure $fun)
{
    probatio()->registerGroup($name, $fun);
}

/**
 * context()
 * @param ?string $name
 * @param \Closure $fun
 * @return void
 */
function context(?string $name, \Closure $fun)
{
    probatio()->registerGroup($name, $fun);
}

/**
 * test()
 * @param ?string $name
 * @param \Closure $fun
 * @return void
 */
function test(?string $name, \Closure $fun)
{
    probatio()->registerTestItem($name, $fun);
}

/**
 * it()
 * @param ?string $name
 * @param \Closure $fun
 * @return void
 */
function it(?string $name, \Closure $fun)
{
    $name = \sprintf('it %s', $name);
    probatio()->registerTestItem($name, $fun);
}

/**
 * beforeAll()
 * @param \Closure $fun
 * @return void
 */
function beforeAll(\Closure $fun)
{
    $hook = new TestHook(TestHook::BEFORE_ALL, $fun);
    probatio()->registerHook($hook);
}

/**
 * afterAll()
 * @param \Closure $fun
 * @return void
 */
function afterAll(\Closure $fun)
{
    $hook = new TestHook(TestHook::AFTER_ALL, $fun);
    probatio()->registerHook($hook);
}

/**
 * beforeEach()
 * @param \Closure $fun
 * @return void
 */
function beforeEach(\Closure $fun)
{
    $hook = new TestHook(TestHook::BEFORE_EACH, $fun);
    probatio()->registerHook($hook);
}

/**
 * afterEach()
 * @param \Closure $fun
 * @return void
 */
function afterEach(\Closure $fun)
{
    $hook = new TestHook(TestHook::AFTER_EACH, $fun);
    probatio()->registerHook($hook);
}

/**
 * expect()
 * @param mixed $value
 * @return Expectation
 */
function expect($value): Expectation
{
    return new Expectation($value);
}
