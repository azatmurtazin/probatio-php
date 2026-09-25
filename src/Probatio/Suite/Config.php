<?php

declare(strict_types=1);

namespace Probatio\Suite;

use Probatio\Utils\Env;
use Probatio\Utils\Printer;

class Config
{
    /** @var string */
    protected $testsDir;

    /** @var string */
    protected $mainFile;

    /** @var bool */
    protected $enableGlobals = true;

    /** @var int */
    protected $verbosity = 0;

    public function __construct()
    {
        $this->testsDir = Env::getStr('PROBATIO_TESTS_DIR', 'tests');
        $this->mainFile = Env::getStr('PROBATIO_MAIN_FILE', "{$this->testsDir}/tests.php");
        $this->enableGlobals = Env::getBool('PROBATIO_REGISTER_GLOBALS', true);
        $this->verbosity = Env::getInt('PROBATIO_VERBOSITY', Printer::VERBOSITY_BRIEF);
    }

    public function testsDir(): string
    {
        return $this->testsDir;
    }

    public function mainFile(): string
    {
        return $this->mainFile;
    }

    public function enableGlobals(): bool
    {
        return $this->enableGlobals;
    }

    public function verbosity(): int
    {
        return $this->verbosity;
    }
}
