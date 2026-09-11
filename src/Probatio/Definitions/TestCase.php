<?php

declare(strict_types=1);

namespace Probatio\Definitions;

use Probatio\Checks\Assertions;
use Probatio\Utils\Printer;

class TestCase
{
    use Assertions;

    /** @var ?TestCase */
    protected $parent = null;

    /** @var array<string, mixed> */
    protected $assigns = [];

    /** @var array<string, \Closure> */
    protected $letters = [];

    public function __construct(?TestCase $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent(): ?TestCase
    {
        return $this->parent;
    }

    /**
     * get()
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $val = $this->assigns[$key] ?? null;

        if ($val !== null) {
            return $val;
        }

        if (isset($this->letters[$key])) {
            $fun = $this->letters[$key]->bindTo($this, $this);
            $val = $fun();
            $this->assigns[$key] = $val;
            return $val;
        }

        if ($this->parent !== null) {
            return $this->parent->get($key);
        }

        return null;
    }

    /**
     * set() - direct assignment of the $value by the $key
     * @param string $key
     * @param mixed $value
     * @return TestCase
     */
    public function set(string $key, $value): self
    {
        $this->assigns[$key] = $value;
        return $this;
    }

    /**
     * let() - stores a closure to initialize and memoize the data
     * @param string $key
     * @param \Closure $fun
     * @return TestCase
     */
    public function let(string $key, \Closure $fun): self
    {
        $this->letters[$key] = $fun;
        return $this;
    }

    public function unset(string $key): self
    {
        unset($this->assigns[$key]);
        return $this;
    }
}
