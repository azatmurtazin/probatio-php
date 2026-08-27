<?php

declare(strict_types=1);

namespace Probatio;

class TestGroup implements ITestNode
{
    /** @var array<string, array<TestHook>> */
    protected $hooks = [
        TestHook::BEFORE_ALL  => [],
        TestHook::AFTER_ALL   => [],
        TestHook::BEFORE_EACH => [],
        TestHook::AFTER_EACH  => [],
    ];

    /** @var ITestNode[] */
    protected $nodes = [];

    /** @var ?string */
    protected $name;

    /** @var \Closure */
    protected $fun;

    /** @var Caller */
    protected $caller;

    public function __construct(?string $name, ?\Closure $fun, Caller $caller)
    {
        $this->name = $name;
        $this->fun = $fun;
        $this->caller = $caller;
    }

    /**
     * getCaller
     * @return Caller
     */
    public function getCaller()
    {
        return $this->caller;
    }

    public function addHook(TestHook $hook)
    {
        $this->hooks[$hook->getType()][] = $hook;
    }

    public function addTestItem(?string $name, \Closure $fun, Caller $caller)
    {
        $testItem = new TestItem($name, $fun, $caller);
        $this->nodes[] = $testItem;
    }

    /**
     * addNestedGroup
     * @param ?string $name
     * @param \Closure $fun
     * @param Caller $caller
     * @return TestGroup new nested group
     */
    public function addNestedGroup(?string $name, \Closure $fun, Caller $caller)
    {
        $group = new TestGroup($name, $fun, $caller);
        $this->nodes[] = $group;
        return $group;
    }

    public function run(TestCase $tc)
    {
        // run hooks and nested nodes (other groups or test items)
        $this->runHooks(TestHook::BEFORE_ALL, $tc);
        foreach ($this->nodes as $node) {
            $this->runHooks(TestHook::BEFORE_EACH, $tc);
            $node->run($tc);
            $this->runHooks(TestHook::AFTER_EACH, $tc);
        }
        $this->runHooks(TestHook::AFTER_ALL, $tc);
    }

    protected function runHooks(string $type, TestCase $tc)
    {
        foreach ($this->hooks[$type] as $hook) {
            $hook->run($tc);
        }
    }
}
