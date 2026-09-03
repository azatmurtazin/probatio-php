<?php

declare(strict_types=1);

namespace Probatio\Definitions;

use Probatio\Runners\SuiteRunner;
use Probatio\Utils\Printer;

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

        $runner = SuiteRunner::getInstance();
        Printer::resetLevel();
        $this->currentGroup = $this->rootGroup;
        Printer::noticeFile("run file {$this->path}\n");
        $this->rootCase = new TestCase();
        $runner->setCurrentCase($this->rootCase);

        $this->rootGroup->run($this->rootCase);

        $this->currentGroup = null;
        $runner->setCurrentCase(null);
        Printer::resetLevel();
    }
}
