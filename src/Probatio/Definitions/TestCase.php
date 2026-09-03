<?php

declare(strict_types=1);

namespace Probatio\Definitions;

use Probatio\Checks\Assertions;

class TestCase
{
    use Assertions;

    /** @var ?TestCase */
    protected $parent = null;

    /** @var array<string, mixed> */
    protected $assigns = [];

    public function __construct(?TestCase $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent(): ?TestCase
    {
        return $this->parent;
    }

    public function get(string $key)
    {
        return $this->assigns[$key];
    }

    public function set(string $key, $value): self
    {
        $this->assigns[$key] = $value;
        return $this;
    }

    public function unset(string $key): self
    {
        unset($this->assigns[$key]);
        return $this;
    }
}
