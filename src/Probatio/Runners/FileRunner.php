<?php

declare(strict_types=1);

namespace Probatio\Runners;

use Probatio\Definitions\TestCase;
use Probatio\Definitions\TestFile;
use Probatio\Definitions\TestGroup;

use function Probatio\Functions\probatio;

use Probatio\Utils\Printer;

class FileRunner implements Runnable
{
    /** @var TestFile */
    protected $testFile;

    /** @var ?TestGroup */
    protected $rootGroup;

    /** @var ?TestGroup */
    protected $currentGroup = null;

    public function __construct(TestFile $testFile)
    {
        $this->testFile = $testFile;
        $this->rootGroup = $testFile->getRootGroup();
    }

    public function runWithTc()
    {
        $this->run(new TestCase());
    }

    public function run(TestCase $tc)
    {
        if ($this->rootGroup === null) {
            return;
        }

        $suiteRunner = probatio()->runner();
        Printer::resetLevel();
        $this->currentGroup = $this->rootGroup;
        $path = $this->testFile->getPath();
        Printer::noticeFile("run file {$path}\n");
        $suiteRunner->setCurrentCase($tc);

        (new GroupRunner($this->rootGroup))->run($tc);

        $this->currentGroup = null;
        $suiteRunner->setCurrentCase(null);
        Printer::resetLevel();
    }
}
