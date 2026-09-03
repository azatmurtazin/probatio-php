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
        try {
            $fun = $this->hook->getFun()->bindTo($tc, $tc);
            $fun();
        } catch (\Throwable $e) {
            $type = $this->hook->getType();
            $loc = $this->hook->getLoc();
            throw new RunnerException("failed to run {$type} hook ($loc)");
        }
    }
}
