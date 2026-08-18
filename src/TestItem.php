<?php

declare(strict_types=1);

namespace Probatio;

use Closure;

class TestItem
{
    /** @var TestCase */
    protected $tc;
    /** @var \Closure */
    protected $fun;
    /** @var array */
    protected $opts = [];
    /** @var \Exception|null */
    protected $error = null;

    use Assertions;

    /**
     * __construct
     * @param TestCase $tc
     * @param \Closure $fun
     * @param array $opts
     */
    public function __construct(TestCase $tc, \Closure $fun, array $opts = [])
    {
        $this->tc = $tc;
        $this->opts = $opts;
        $this->fun = $fun->bindTo($this, __CLASS__);
    }

    public function run()
    {
        $name = $this->opts["name"] ?? null;
        $file = $this->opts["file"] ?? null;
        $line = $this->opts["line"] ?? null;
        $title = Utils::getTitle($name, $file, $line);
        echo "  * $title\n";
        $this->tc->counterInc();
        try {
            ($this->fun)($this->tc);
            echo "  ✅ ok\n";
            $this->tc->okCounterInc();
        } catch (\Exception $e) {
            $this->error = $e;
            $this->tc->registerFailure($this);
            echo "  ❌ error: " . $e->getMessage() . "\n";
        }
    }
}
