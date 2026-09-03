<?php

declare(strict_types=1);

namespace Probatio\Suite;

class TestStats
{
    /** @var ?self */
    protected static $instance;

    /** @var int */
    protected $okTests = 0;

    /** @var int */
    protected $errTests = 0;

    /** @var int */
    protected $okAsserts = 0;

    /** @var int */
    protected $errAsserts = 0;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getOkTests(): int
    {
        return $this->okTests;
    }

    public function incOkTests(): self
    {
        $this->okTests++;
        return $this;
    }

    public function getErrTests(): int
    {
        return $this->errTests;
    }

    public function incErrTests(): self
    {
        $this->errTests++;
        return $this;
    }

    public function getOkAsserts(): int
    {
        return $this->okAsserts;
    }

    public function incOkAsserts(): self
    {
        $this->okAsserts++;
        return $this;
    }

    public function getErrAsserts(): int
    {
        return $this->errAsserts;
    }

    public function incErrAsserts(): self
    {
        $this->errAsserts++;
        return $this;
    }
}
