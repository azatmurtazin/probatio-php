<?php

declare(strict_types=1);

namespace Probatio\Runners;

use Probatio\Definitions\Definition;
use Probatio\Definitions\TestCase;
use Probatio\Definitions\TestGroup;
use Probatio\Definitions\TestHook;
use Probatio\Definitions\TestItem;

use function Probatio\Functions\probatio;

use Probatio\Utils\Printer;

class GroupRunner implements Runnable
{
    /** @var TestGroup */
    protected $group;

    /** @var array<string, TestHook[]> */
    protected $hooks;

    /** @var array<TestGroup|TestItem> */
    protected $nodes = [];

    public function __construct(TestGroup $group)
    {
        $this->group = $group;
        $this->hooks = $group->getHooks();
        $this->nodes = $group->getNodes();
    }

    public function run(TestCase $tc)
    {
        $runner = probatio()->runner();

        $name = $this->group->getName();
        $loc = $this->group->getLoc();

        if ($loc && !$loc->empty()) {
            Printer::incLevel();
            $title = (string) $loc->withName($name);
            Printer::noticeGroup("test group '$title'");
        }

        $this->runHooks(TestHook::BEFORE_ALL, $tc);
        foreach ($this->nodes as $node) {
            $this->runHooks(TestHook::BEFORE_EACH, $tc);
            if ($node instanceof TestGroup) {
                $oldTc = $runner->getCurrentCase();
                $newTc = new TestCase($tc);
                $runner->setCurrentCase($tc);
                (new GroupRunner($node))->run($newTc);
                $runner->setCurrentCase($oldTc);
            } elseif ($node instanceof TestItem) {
                (new ItemRunner($node))->run($tc);
            }
            $this->runHooks(TestHook::AFTER_EACH, $tc);
        }
        $this->runHooks(TestHook::AFTER_ALL, $tc);

        if ($loc && !$loc->empty()) {
            Printer::decLevel();
        }
    }

    protected function runHooks(string $type, TestCase $tc)
    {
        foreach ($this->hooks[$type] as $hook) {
            (new HookRunner($hook))->run($tc);
        }
    }
}
