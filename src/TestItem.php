<?php

declare(strict_types=1);

namespace Probatio;

class TestItem implements ITestNode
{
    /** @var ?string */
    protected $name = null;
    /** @var \Closure */
    protected $fun;
    /** @var Caller */
    protected $caller;
    /** @var \Exception|null */
    protected $error = null;

    public function __construct(?string $name, \Closure $fun, Caller $caller)
    {
        $this->name = $name;
        $this->caller = $caller;
        $this->fun = $fun;
    }

    public function run(TestCase $tc)
    {
        [$file, $line] = $this->caller->fl();
        $title = Utils::getTitle($this->name, $file, $line);
        echo "  * $title\n";

        $fun = $this->fun->bindTo($tc, $tc);
        // $tc->counterInc();

        try {
            $fun();
            echo "  ✅ ok\n";
            TestSuite::getInstance()->incrOkTests();
            // $tc->okCounterInc();
        } catch (\Throwable $e) {
            $this->error = $e;
            // $tc->registerFailure($this);
            TestSuite::getInstance()->incrErrTests();
            echo "  ❌ error: " . $e->getMessage() . "\n";
        }
    }
}
