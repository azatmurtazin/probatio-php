<?php

declare(strict_types=1);

namespace Epreuve;

class TestCase
{
    use Assertions;

    protected array $opts = [];
    protected array $assigns = [];
    /** @var TestItem[] */
    protected array $testItems = [];
    protected mixed $beforeFun = null;
    protected mixed $afterFun = null;
    protected int $counter = 0;
    protected int $okCounter = 0;
    /** @var TestItem[] */
    protected array $failures = [];

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

    public function get(string $key): mixed
    {
        return $this->assigns[$key];
    }

    public function set(string $key, mixed $value): self
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
