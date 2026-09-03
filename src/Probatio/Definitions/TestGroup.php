<?php

declare(strict_types=1);

namespace Probatio\Definitions;

use Probatio\Utils\Location;

class TestGroup implements Definition
{
    /** @var array<string, TestHook[]> */
    protected $hooks = [
        TestHook::BEFORE_ALL  => [],
        TestHook::AFTER_ALL   => [],
        TestHook::BEFORE_EACH => [],
        TestHook::AFTER_EACH  => [],
    ];

    /** @var array<TestGroup|TestItem> */
    protected $nodes = [];

    /** @var ?string */
    protected $name;

    /** @var \Closure */
    protected $fun;

    /** @var ?Location */
    protected $loc;

    public function __construct(?string $name = null, ?\Closure $fun = null)
    {
        $this->name = $name;
        $this->fun = $fun;
        $this->loc = Location::fromFun($fun);
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
     * addNestedGroup()
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLoc(): ?Location
    {
        return $this->loc;
    }
    public function getFun(): ?\Closure
    {
        return $this->fun;
    }

    /**
     * getHooks()
     * @return array<string, TestHook[]>
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }

    /**
     * getNodes()
     * @return array<TestGroup|TestItem>
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }
}
