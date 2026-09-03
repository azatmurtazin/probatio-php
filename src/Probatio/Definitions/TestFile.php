<?php

declare(strict_types=1);

namespace Probatio\Definitions;

class TestFile
{
    /** @var string */
    protected $path;

    /** @var ?TestGroup */
    protected $rootGroup;

    /** @var ?TestGroup */
    protected $currentGroup;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRootGroup(): ?TestGroup
    {
        return $this->rootGroup;
    }

    public function getCurrentGroup(): TestGroup
    {
        if ($this->currentGroup === null) {
            $this->rootGroup = new TestGroup();
            $this->currentGroup = $this->rootGroup;
        }

        return $this->currentGroup;
    }

    public function registerGroup(?string $name, \Closure $fun): self
    {
        $oldCurrentGroup = $this->getCurrentGroup();
        $newGroup = $oldCurrentGroup->addNestedGroup($name, $fun);
        $this->currentGroup = $newGroup;
        (function () use ($fun) {
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

    public function registerTestItem(?string $name, \Closure $fun): self
    {
        $this->getCurrentGroup()->addTestItem($name, $fun);
        return $this;
    }
}
