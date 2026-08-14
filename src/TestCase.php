<?php

declare(strict_types=1);

namespace Probatio;

class TestCase
{
    use Assertions;

    /** @var array */
    protected $opts = [];
    /** @var array */
    protected $assigns = [];
    /** @var TestItem[] */
    protected $testItems = [];
    /** @var callable|null */
    protected $beforeFun = null;
    /** @var callable|null */
    protected $afterFun = null;
    /** @var int */
    protected $counter = 0;
    /** @var int */
    protected $okCounter = 0;
    /** @var TestItem[] */
    protected $failures = [];

    public function __construct(array $opts = [])
    {
        $this->opts = $opts;
    }

    public function getName(): ?string
    {
        return $this->opts["name"] ?? null;
    }

    public function getFile(): ?string
    {
        return $this->opts["file"] ?? null;
    }

    public function getLine(): ?int
    {
        return $this->opts["line"] ?? null;
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
        $name = $this->getName();
        $file = $this->getFile();
        $line = $this->getLine();
        echo "* $name ($file:$line) ...\n";
        $this->execBefore();
        $tiKeys = array_keys($this->testItems);
        shuffle($tiKeys);
        foreach ($tiKeys as $tiKey) {
            $this->testItems[$tiKey]->run();
        }
        $this->execAfter();
        echo "\n";
        return $this;
    }

    public function before(callable $fun): self
    {
        $this->beforeFun = $fun;
        return $this;
    }

    public function after(callable $fun): self
    {
        $this->afterFun = $fun;
        return $this;
    }

    public function test(?string $name, callable $fun): self
    {
        [$file, $line] = Utils::getCaller();
        $opts = ["file" => $file, "line" => $line];
        if ($name !== null) {
            $opts["name"] = $name;
        }
        $ti = new TestItem($this, $fun, $opts);
        $this->testItems[] = $ti;
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
        if (is_callable($this->beforeFun)) {
            $beforeFun = $this->beforeFun;
            $beforeFun($this);
        }
        return $this;
    }

    public function execAfter()
    {
        if (is_callable($this->afterFun)) {
            $afterFun = $this->afterFun;
            $afterFun($this);
        }
        return $this;
    }
}
