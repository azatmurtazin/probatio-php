<?php

declare(strict_types=1);

namespace Probatio\Runners;

use Probatio\Definitions\TestCase;
use Probatio\Definitions\TestHook;

class HookRunner implements Runnable
{
    /** @var TestHook */
    protected $hook;

    public function __construct(TestHook $hook)
    {
        $this->hook = $hook;
    }

    public function run(TestCase $tc)
    {
        $type = $this->hook->getType();
        $fun = $this->hook->getFun();
        $name = $this->hook->getName();

        if ($type === TestHook::LET) {
            $fun = function () use ($tc, $fun, $name) {
                $tc->let($name, $fun);
            };
        } elseif ($type === TestHook::SET) {
            $fun = function () use ($tc, $fun, $name) {
                $val = $fun();
                $tc->set($name, $val);
            };
        }

        try {
            $fun = $fun->bindTo($tc, $tc);
            $fun();
        } catch (\Throwable $e) {
            $type = $this->hook->getType();
            $loc = $this->hook->getLoc();
            throw new RunnerException("failed to run {$type} hook ($loc)");
        }
    }
}
