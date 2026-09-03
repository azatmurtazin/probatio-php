<?php

declare(strict_types=1);

namespace Probatio\Suite;

use Probatio\Utils\Env;

class Config
{
    /** @var string */
    protected $testsDir;

    /** @var string */
    protected $mainFile;

    /** @var bool */
    protected $enableGlobals = true;

    public function __construct()
    {
        $this->testsDir = Env::getStr('PROBATIO_TESTS_DIR', 'tests');
        $this->mainFile = Env::getStr('PROBATIO_MAIN_FILE', "{$this->testsDir}/tests.php");
        $this->enableGlobals = Env::getBool('PROBATIO_REGISTER_GLOBALS', true);
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
}
