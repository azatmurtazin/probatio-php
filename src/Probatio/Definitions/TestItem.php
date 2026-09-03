<?php

declare(strict_types=1);

namespace Probatio\Definitions;

use Probatio\Runners\SuiteRunner;
use Probatio\Suite\TestStats;
use Probatio\Tools\Location;
use Probatio\Tools\Printer;

class TestItem implements Runnable
{
    /** @var ?string */
    protected $name = null;

    /** @var \Closure */
    protected $fun;

    /** @var Location */
    protected $loc;

    /** @var \Exception|null */
    protected $error = null;

    public function __construct(?string $name, \Closure $fun)
    {
        $this->name = $name;
        $this->fun = $fun;
        $this->loc = Location::fromFun($fun);
    }

    public function run(TestCase $tc)
    {
        $runner = SuiteRunner::getInstance();
        $stats = TestStats::getInstance();

        $runner->incLevel();
        $title = (string) $this->loc->withName($this->name);
        Printer::noticeItem("test $title");

        $fun = $this->fun->bindTo($tc, $tc);

        $runner->incLevel();
        $result = 'ok';

        try {
            $fun();
            $stats->incOkTests();
        } catch (\Throwable $e) {
            $this->error = $e;
            $errClass = \get_class($e);
            $msg = $e->getMessage();
            [$f, $l] = Location::fromException($e)->toArray();
            Printer::noticeErr("$errClass: $msg ($f:$l)");
            $stats->incErrTests();
            $result = 'err';
        }

        $runner->decLevel();

        if ($result === 'ok') {
            Printer::noticeOk("test '{$this->name}' is ok\n");
        } else {
            Printer::noticeErr("test '{$this->name}' failed\n");
        }

        $runner->decLevel();
    }
}
