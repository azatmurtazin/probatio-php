<?php

declare(strict_types=1);

namespace Probatio;

class TestRegistry
{
    /** @var array<string, TestFile> */
    protected $testFiles = [];

    /** @var ?TestFile */
    protected $currentTestFile;

    public function registerTestFile(string $path): self
    {
        $this->currentTestFile = new TestFile($path);

        (function () use ($path) {
            require_once $path;
        })();

        $this->currentTestFile->finalize();
        $this->testFiles[$path] = $this->currentTestFile;
        $this->currentTestFile = null;

        return $this;
    }

    public function getCurrentFile(): TestFile
    {
        if ($this->currentTestFile === null) {
            throw new \RuntimeException('Current test file not found');
        }
        return $this->currentTestFile;
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

    public function runRegisteredTests()
    {
        $keys = array_keys($this->testFiles);
        shuffle($keys);

        foreach ($keys as $key) {
            $testFile = $this->testFiles[$key];
            $this->currentTestFile = $testFile;
            $testFile->runWithTc();
            $this->currentTestFile = null;
        }
    }

    public function getOkTests(): int
    {
        $fun = function (TestFile $tf) {
            return $tf->getOkTests();
        };
        return \array_sum(\array_map($fun, $this->testFiles));
    }

    public function getErrTests(): int
    {
        $fun = function (TestFile $tf) {
            return $tf->getErrTests();
        };
        return \array_sum(\array_map($fun, $this->testFiles));
    }
}
