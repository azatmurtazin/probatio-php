<?php

declare(strict_types=1);

namespace Probatio;

class TestItem
{
    /** @var TestCase */
    protected $tc;
    /** @var callable */
    protected $fun;
    /** @var array */
    protected $opts = [];
    /** @var \Exception|null */
    protected $error = null;

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
