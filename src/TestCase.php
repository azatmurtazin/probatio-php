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
    /** @var TestItem[] */
    protected $testItems = [];
    /** @var \Closure|null */
    protected $beforeFun = null;
    /** @var \Closure|null */
    protected $afterFun = null;
    /** @var int */
    protected $counter = 0;
    /** @var int */
    protected $okCounter = 0;
    /** @var TestItem[] */
    protected $failures = [];

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

    public function isOk(): bool
    {
        return $this->counter == $this->okCounter;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function counterInc(): void
    {
        $this->counter++;
    }

    public function getOkCounter(): int
    {
        return $this->okCounter;
    }

    public function okCounterInc(): void
    {
        $this->okCounter++;
    }

    public function registerFailure(TestItem $ti): void
    {
        $this->failures[] = $ti;
    }

    public function run(): self
    {
        [$file, $line] = $this->caller->fl();
        $title = Utils::getTitle($this->name, $file, $line);
        echo "* $title ...\n";
        $this->execBefore();
        $tiKeys = array_keys($this->testItems);
        shuffle($tiKeys);
        foreach ($tiKeys as $tiKey) {
            $this->testItems[$tiKey]->run($this);
        }
        $this->execAfter();
        echo "\n";
        return $this;
    }

    public function before(\Closure $fun): self
    {
        $this->beforeFun = $fun;
        return $this;
    }

    public function after(\Closure $fun): self
    {
        $this->afterFun = $fun;
        return $this;
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

    public function execBefore()
    {
        if ($this->beforeFun instanceof \Closure) {
            $beforeFun = $this->beforeFun;
            $beforeFun($this);
        }
        return $this;
    }

    public function execAfter()
    {
        if ($this->afterFun instanceof \Closure) {
            $afterFun = $this->afterFun;
            $afterFun($this);
        }
        return $this;
    }
}
