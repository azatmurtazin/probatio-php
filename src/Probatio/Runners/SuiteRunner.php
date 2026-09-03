<?php

declare(strict_types=1);

namespace Probatio\Runners;

use Probatio\Definitions\TestCase;
use Probatio\Definitions\TestFile;

class SuiteRunner
{
    /** @var TestFile[] */
    protected $testFiles;

    /** @var ?TestFile */
    protected $currentFile = null;

    /** @var ?TestCase */
    protected $currentCase = null;

    public function setFiles(array $testFiles): self
    {
        $this->testFiles = $testFiles;
        return $this;
    }

    public function getCurrentFile(): TestFile
    {
        return $this->currentFile;
    }

    public function getCurrentCase(): ?TestCase
    {
        return $this->currentCase;
    }

    public function setCurrentCase(?TestCase $tc): self
    {
        $this->currentCase = $tc;
        return $this;
    }

    public function run()
    {
        $keys = array_keys($this->testFiles);
        shuffle($keys);

        foreach ($keys as $key) {
            $testFile = $this->testFiles[$key];
            $this->currentFile = $testFile;
            (new FileRunner($testFile))->run(new TestCase());
            $this->currentFile = null;
        }
    }
}
