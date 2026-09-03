<?php

declare(strict_types=1);

namespace Probatio\Runners;

use Probatio\Definitions\TestCase;
use Probatio\Definitions\TestItem;

use function Probatio\Functions\probatio;

use Probatio\Utils\Location;
use Probatio\Utils\Printer;

class ItemRunner implements Runnable
{
    /** @var TestItem */
    protected $testItem;

    public function __construct(TestItem $testItem)
    {
        $this->testItem = $testItem;
    }

    public function run(TestCase $tc)
    {
        $stats = probatio()->stats();

        $name = $this->testItem->getName();
        $loc = $this->testItem->getLoc();
        $fun = $this->testItem->getFun();
        $fun = $fun->bindTo($tc, $tc);

        Printer::incLevel();
        $title = (string) $loc->withName($name);
        Printer::noticeItem("test $title");

        Printer::incLevel();
        $result = 'ok';

        try {
            $fun();
            $stats->incOkTests();
        } catch (\Throwable $e) {
            $errClass = \get_class($e);
            $msg = $e->getMessage();
            $loc = Location::fromException($e);
            Printer::noticeErr("$errClass: $msg ($loc)");
            $stats->incErrTests();
            $result = 'err';
        }

        Printer::decLevel();

        if ($result === 'ok') {
            Printer::noticeOk("test '{$name}' is ok\n");
        } else {
            Printer::noticeErr("test '{$name}' failed\n");
        }

        Printer::decLevel();
    }
}
