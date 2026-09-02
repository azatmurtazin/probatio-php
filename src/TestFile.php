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
    protected $rootCase;

    /** @var ?TestCase */
    protected $currentCase;

    /** @var int */
    protected $okTests = 0;

    /** @var int */
    protected $errTests = 0;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getCurrentGroup(): TestGroup
    {
        if ($this->currentGroup === null) {
            $this->rootGroup = new TestGroup();
            $this->currentGroup = $this->rootGroup;
        }

        return $this->currentGroup;
    }

    public function getCurrentCase(): TestCase
    {
        if ($this->currentCase === null) {
            throw new \RuntimeException('Current test case not found');
        }
        return $this->currentCase;
    }

    public function setCurrentCase(?TestCase $tc): self
    {
        $this->currentCase = $tc;
        return $this;
    }

    public function finalize()
    {
        // do something useful
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

    public function runWithTc()
    {
        if ($this->rootGroup === null) {
            return;
        }

        TestRunner::getInstance()->resetLevel();
        $this->currentGroup = $this->rootGroup;
        Printer::noticeFile("run file {$this->path}\n");
        $this->rootCase = new TestCase();
        $this->setCurrentCase($this->rootCase);
        $this->rootGroup->run($this->rootCase);
        $this->currentGroup = null;
        $this->setCurrentCase(null);
        TestRunner::getInstance()->resetLevel();
    }

    public function incrOkTests(): self
    {
        $this->okTests++;
        return $this;
    }

    public function incrErrTests(): self
    {
        $this->errTests++;
        return $this;
    }

    public function getOkTests(): int
    {
        return $this->okTests;
    }

    public function getErrTests(): int
    {
        return $this->errTests;
    }
}
