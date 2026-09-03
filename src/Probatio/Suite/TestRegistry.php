<?php

declare(strict_types=1);

namespace Probatio\Suite;

use Probatio\Definitions\TestFile;
use Probatio\Definitions\TestHook;

class TestRegistry
{
    /** @var array<string, TestFile> */
    protected $testFiles = [];

    /** @var ?TestFile */
    protected $currentFile;

    /**
     * getTestFiles()
     * @return array<string, TestFile>
     */
    public function getTestFiles(): array
    {
        return $this->testFiles;
    }

    public function registerTestFile(string $path): self
    {
        $this->currentFile = new TestFile($path);

        (function () use ($path) {
            require_once $path;
        })();

        $this->testFiles[$path] = $this->currentFile;
        $this->currentFile = null;

        return $this;
    }

    public function getCurrentFile(): TestFile
    {
        if ($this->currentFile === null) {
            throw new \RuntimeException('Current test file not found');
        }
        return $this->currentFile;
    }

    public function registerGroup(?string $name, \Closure $fun): self
    {
        $this->getCurrentFile()->registerGroup($name, $fun);
        return $this;
    }

    public function registerTestItem(?string $name, \Closure $fun): self
    {
        $this->getCurrentFile()->registerTestItem($name, $fun);
        return $this;
    }

    public function registerHook(TestHook $hook): self
    {
        $this->getCurrentFile()->registerHook($hook);
        return $this;
    }
}
