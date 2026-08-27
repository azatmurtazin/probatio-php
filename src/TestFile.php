<?php

declare(strict_types=1);

namespace Probatio;

class TestFile
{
    /** @var string */
    protected $path;

    /** @var ?TestGroup */
    protected $rootGroup;

    /** @var ?TestGroup */
    protected $currentGroup;

    /** @var ?TestCase */
    protected $tc;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getCurrentGroup(): TestGroup
    {
        if ($this->currentGroup === null) {
            $this->rootGroup = new TestGroup(null, null, new Caller());
            $this->currentGroup = $this->rootGroup;
        }

        return $this->currentGroup;
    }

    public function finalize()
    {
        // do something useful
    }

    public function registerGroup(?string $name, \Closure $fun, Caller $caller): self
    {
        $oldCurrentGroup = $this->getCurrentGroup();
        $newGroup = $oldCurrentGroup->addNestedGroup($name, $fun, $caller);
        $this->currentGroup = $newGroup;
        (function() use($fun) {
            $fun();
        })();
        $this->currentGroup = $oldCurrentGroup;

        return $this;
    }

    public function registerHook(TestHook $hook): self
    {
        $this->getCurrentGroup()->addHook($hook);
        return $this;
    }

    public function registerTestItem(?string $name, \Closure $fun, Caller $caller): self
    {
        $this->getCurrentGroup()->addTestItem($name, $fun, $caller);
        return $this;
    }

    public function runWithTc()
    {
        if ($this->rootGroup === null) return;

        $this->tc = new TestCase(null, $this->rootGroup->getCaller());
        $this->rootGroup->run($this->tc);
    }
}
