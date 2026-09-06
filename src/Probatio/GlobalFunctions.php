<?php

declare(strict_types=1);

use Probatio\Functions;
use Probatio\Suite\TestSuite;

$enabledGlobals = TestSuite::getInstance()->config()->enableGlobals();

$canRegisterFunction = function (string $name) use ($enabledGlobals) {
    if ($enabledGlobals && function_exists($name)) {
        throw new \RuntimeException("Cannot register global function `{$name}`, use the namespaced one");
    }
    return $enabledGlobals;
};

if ($canRegisterFunction('probatio')) {
    function probatio(?string $name, \Closure $fun)
    {
        return Functions\probatio();
    }
}

if ($canRegisterFunction('describe')) {
    function describe(?string $name, \Closure $fun)
    {
        return Functions\describe($name, $fun);
    }
}

if ($canRegisterFunction('context')) {
    function context(?string $name, \Closure $fun)
    {
        return Functions\context($name, $fun);
    }
}

if ($canRegisterFunction('test')) {
    function test(?string $name, \Closure $fun)
    {
        return Functions\test($name, $fun);
    }
}

if ($canRegisterFunction('it')) {
    function it(?string $name, \Closure $fun)
    {
        return Functions\it($name, $fun);
    }
}

if ($canRegisterFunction('expect')) {
    function expect($value)
    {
        return Functions\expect($value);
    }
}

if ($canRegisterFunction('beforeAll')) {
    function beforeAll(\Closure $fun)
    {
        return Functions\beforeAll($fun);
    }
}

if ($canRegisterFunction('afterAll')) {
    function afterAll(\Closure $fun)
    {
        return Functions\afterAll($fun);
    }
}

if ($canRegisterFunction('beforeEach')) {
    function beforeEach(\Closure $fun)
    {
        return Functions\beforeEach($fun);
    }
}

if ($canRegisterFunction('afterEach')) {
    function afterEach(\Closure $fun)
    {
        return Functions\afterEach($fun);
    }
}
