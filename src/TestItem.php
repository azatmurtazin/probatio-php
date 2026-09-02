<?php

declare(strict_types=1);

namespace Probatio;

class TestItem implements Runnable
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
        TestRunner::getInstance()->incLevel();
        [$file, $start, $end] = $this->loc->toArray();
        $title = Utils::getTitle($this->name, $file, $start, $end);
        Printer::noticeItem("test $title");

        $fun = $this->fun->bindTo($tc, $tc);

        TestRunner::getInstance()->incLevel();
        $result = 'ok';

        try {
            $fun();
            // Printer::noticeOk("test '{$this->name}' is ok");
            TestSuite::getInstance()->incrOkTests();
        } catch (\Throwable $e) {
            $this->error = $e;
            $errClass = \get_class($e);
            $msg = $e->getMessage();
            [$f, $l] = CodeLoc::fromException($e)->toArray();
            Printer::noticeErr("$errClass: $msg ($f:$l)");
            TestSuite::getInstance()->incrErrTests();
            $result = 'err';
        }

        TestRunner::getInstance()->decLevel();

        if ($result === 'ok') {
            Printer::noticeOk("test '{$this->name}' is ok\n");
        } else {
            Printer::noticeErr("test '{$this->name}' failed\n");
        }

        TestRunner::getInstance()->decLevel();
    }
}
