<?php

declare(strict_types=1);

namespace Probatio;

class TestRunner
{
    /** @var ?self */
    protected static $instance;

    /** @var int */
    protected $level = 0;

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

    public function getLevel(): int
    {
        return $this->level;
    }

    public function resetLevel()
    {
        $this->level = 0;
    }

    public function incLevel()
    {
        $this->level++;
    }

    public function decLevel()
    {
        $this->level--;
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
