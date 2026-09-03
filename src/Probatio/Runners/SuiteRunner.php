<?php

declare(strict_types=1);

namespace Probatio\Runners;

use Probatio\Definitions\TestCase;
use Probatio\Definitions\TestFile;

class SuiteRunner
{
    /** @var ?self */
    protected static $instance;

    /** @var ?TestFile */
    protected $currentFile = null;

    /** @var ?TestCase */
    protected $currentCase = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
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
}
