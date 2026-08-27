<?php

declare(strict_types=1);

namespace Probatio;

class TestCase
{
    use Assertions;

    /** @var ?TestCase */
    protected $parent = null;

    /** @var ?string */
    protected $name = null;

    /** @var Caller */
    protected $caller;

    /** @var array<string, mixed> */
    protected $assigns = [];

    public function __construct(?string $name, Caller $caller, ?TestCase $parent = null)
    {
        $this->name = $name;
        $this->caller = $caller;
        $this->parent = $parent;
    }

    public function getFile(): ?string
    {
        [$file] = $this->caller->fl();
        return $file;
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
