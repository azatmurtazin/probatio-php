<?php

declare(strict_types=1);

use Probatio\Functions;
use Probatio\Utils\Env;

$enabledGlobalFun = Env::getBool('PROBATIO_REGISTER_GLOBALS', true);
$errorMsgTpl = 'Cannot register global function %s(), use the namespaced one';

if ($enabledGlobalFun) {
    if (!function_exists('describe')) {
        function describe(?string $name, \Closure $fun)
        {
            return Functions\describe($name, $fun);
        }
    } else {
        throw new \RuntimeException(sprintf($errorMsgTpl, 'describe'));
    }
}

if ($enabledGlobalFun) {
    if (!function_exists('context')) {
        function context(?string $name, \Closure $fun)
        {
            return Functions\context($name, $fun);
        }
    } else {
        throw new \RuntimeException(sprintf($errorMsgTpl, 'context'));
    }
}

if ($enabledGlobalFun) {
    if (!function_exists('test')) {
        function test(?string $name, \Closure $fun)
        {
            return Functions\test($name, $fun);
        }
    } else {
        throw new \RuntimeException(sprintf($errorMsgTpl, 'test'));
    }
}

if ($enabledGlobalFun) {
    if (!function_exists('it')) {
        function it(?string $name, \Closure $fun)
        {
            return Functions\it($name, $fun);
        }
    } else {
        throw new \RuntimeException(sprintf($errorMsgTpl, 'it'));
    }
}

if ($enabledGlobalFun) {
    if (!function_exists('expect')) {
        function expect($value)
        {
            return Functions\expect($value);
        }
    } else {
        throw new \RuntimeException(sprintf($errorMsgTpl, 'expect'));
    }
}

if ($enabledGlobalFun) {
    if (!function_exists('beforeAll')) {
        function beforeAll(\Closure $fun)
        {
            return Functions\beforeAll($fun);
        }
    } else {
        throw new \RuntimeException(sprintf($errorMsgTpl, 'beforeAll'));
    }
}

if ($enabledGlobalFun) {
    if (!function_exists('afterAll')) {
        function afterAll(\Closure $fun)
        {
            return Functions\afterAll($fun);
        }
    } else {
        throw new \RuntimeException(sprintf($errorMsgTpl, 'afterAll'));
    }
}

if ($enabledGlobalFun) {
    if (!function_exists('beforeEach')) {
        function beforeEach(\Closure $fun)
        {
            return Functions\beforeEach($fun);
        }
    } else {
        throw new \RuntimeException(sprintf($errorMsgTpl, 'beforeEach'));
    }
}

if ($enabledGlobalFun) {
    if (!function_exists('afterEach')) {
        function afterEach(\Closure $fun)
        {
            return Functions\afterEach($fun);
        }
    } else {
        throw new \RuntimeException(sprintf($errorMsgTpl, 'afterEach'));
    }
}
