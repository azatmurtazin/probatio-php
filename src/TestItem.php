<?php

declare(strict_types=1);

namespace Probatio;

class TestItem implements ITestNode
{
    /** @var ?string */
    protected $name = null;

    /** @var \Closure */
    protected $fun;

    /** @var CodeLoc */
    protected $loc;

    /** @var \Exception|null */
    protected $error = null;

    public function __construct(?string $name, \Closure $fun)
    {
        $this->name = $name;
        $this->fun = $fun;
        $this->loc = CodeLoc::fromFun($fun);
    }

    public function run(TestCase $tc)
    {
        [$file, $start, $end] = $this->loc->toArray();
        $title = Utils::getTitle($this->name, $file, $start, $end);
        echo "  🔹 test $title\n";

        $fun = $this->fun->bindTo($tc, $tc);

        try {
            $fun();
            echo "  ✅ ok\n";
            TestSuite::getInstance()->incrOkTests();
        } catch (\Throwable $e) {
            $this->error = $e;
            TestSuite::getInstance()->incrErrTests();
            $errClass = \get_class($e);
            echo "    ❌ $errClass: " . $e->getMessage() . "\n";
        }
    }
}
