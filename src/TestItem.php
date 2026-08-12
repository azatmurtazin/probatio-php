<?php

declare(strict_types=1);

namespace Epreuve;

class TestItem
{
    protected TestCase $tc;
    protected mixed $fun;
    protected array $opts = [];
    protected \Exception|null $error = null;

    public function __construct(TestCase $tc, callable $fun, array $opts = [])
    {
        $this->tc = $tc;
        $this->fun = $fun;
        $this->opts = $opts;
    }

    public function run()
    {
        $name = $this->opts["name"] ?? null;
        $file = $this->opts["file"] ?? null;
        $line = $this->opts["line"] ?? null;
        echo "  * $name ($file:$line)\n";
        $this->tc->counterInc();
        try {
            $fun = $this->fun;
            $fun($this->tc);
            echo "  ✅ ok\n";
            $this->tc->okCounterInc();
        } catch(\Exception $e) {
            $this->error = $e;
            $this->tc->registerFailure($this);
            echo "  ❌ error: ".$e->getMessage()."\n";
        }
    }
}
