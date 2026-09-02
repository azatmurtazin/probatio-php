<?php

declare(strict_types=1);

namespace Probatio;

class TestGroup implements Runnable
{
    /** @var array<string, array<TestHook>> */
    protected $hooks = [
        TestHook::BEFORE_ALL  => [],
        TestHook::AFTER_ALL   => [],
        TestHook::BEFORE_EACH => [],
        TestHook::AFTER_EACH  => [],
    ];

    /** @var Runnable[] */
    protected $nodes = [];

    /** @var ?string */
    protected $name;

    /** @var \Closure */
    protected $fun;

    /** @var ?CodeLoc */
    protected $loc;

    public function __construct(?string $name = null, ?\Closure $fun = null)
    {
        $this->name = $name;
        $this->fun = $fun;
        $this->loc = CodeLoc::fromFun($fun);
    }

    public function addHook(TestHook $hook)
    {
        $this->hooks[$hook->getType()][] = $hook;
    }

    public function addTestItem(?string $name, \Closure $fun)
    {
        $testItem = new TestItem($name, $fun);
        $this->nodes[] = $testItem;
    }

    /**
     * addNestedGroup
     * @param ?string $name
     * @param \Closure $fun
     * @return TestGroup new nested group
     */
    public function addNestedGroup(?string $name, \Closure $fun)
    {
        $group = new TestGroup($name, $fun);
        $this->nodes[] = $group;
        return $group;
    }

    public function run(TestCase $tc)
    {
        if ($this->loc) {
            TestRunner::getInstance()->incLevel();

            [$file, $start, $end] = $this->loc->toArray();
            $title = Utils::getTitle($this->name, $file, $start, $end);
            Printer::noticeGroup("test group: $title");
        }

        $registry = TestSuite::getInstance()->getRegistry();

        $this->runHooks(TestHook::BEFORE_ALL, $tc);
        foreach ($this->nodes as $node) {
            $this->runHooks(TestHook::BEFORE_EACH, $tc);
            if ($node instanceof TestGroup) {
                $oldTc = $registry->getCurrentCase();
                $newTc = new TestCase($tc);
                $registry->setCurrentCase($tc);
                $node->run($newTc);
                $registry->setCurrentCase($oldTc);
            } else {
                $node->run($tc);
            }
            $this->runHooks(TestHook::AFTER_EACH, $tc);
        }
        $this->runHooks(TestHook::AFTER_ALL, $tc);

        if ($this->loc) {
            TestRunner::getInstance()->decLevel();
        }
    }

    protected function runHooks(string $type, TestCase $tc)
    {
        foreach ($this->hooks[$type] as $hook) {
            $hook->run($tc);
        }
    }
}
