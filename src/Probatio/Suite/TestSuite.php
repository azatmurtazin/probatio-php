<?php

declare(strict_types=1);

namespace Probatio\Suite;

use Probatio\Definitions\TestHook;
use Probatio\Runners\SuiteRunner;
use Probatio\Utils\Path;
use Probatio\Utils\Printer;

class TestSuite
{
    /** @var self|null */
    protected static $instance = null;

    /** @var Config */
    protected $config;

    /** @var string[] */
    protected $cliArgs = [];

    /** @var string[] */
    protected $tcFiles = [];

    /** @var TestRegistry */
    protected $registry;

    /** @var TestStats */
    protected $stats;

    /** @var SuiteRunner */
    protected $runner;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct()
    {
        global $argv;
        $this->cliArgs = $argv;
        \array_shift($this->cliArgs);
        foreach ($this->cliArgs as $cliArg) {
            if (Path::isTestCaseFile($cliArg)) {
                $this->tcFiles[] = $cliArg;
            }
        }
        $this->config = new Config();
        $this->registry = new TestRegistry();
        $this->stats = new TestStats();
        $this->runner = new SuiteRunner();
    }

    public function config(): Config
    {
        return $this->config;
    }

    /**
     * stats()
     * @return TestStats
     */
    public function stats(): TestStats
    {
        return $this->stats;
    }

    /**
     * runner()
     * @return SuiteRunner
     */
    public function runner(): SuiteRunner
    {
        return $this->runner;
    }

    public function getRegistry(): TestRegistry
    {
        return $this->registry;
    }

    public function registerTestFiles(): self
    {
        if (empty($this->tcFiles)) {
            Printer::info("run all tests\n");
            $this->registerAllTestFiles();
        } else {
            Printer::info('run test files: ' . implode(', ', $this->tcFiles) . "\n");
            foreach ($this->tcFiles as $tcFile) {
                $this->registerTestFile($tcFile);
            };
        }

        return $this;
    }

    public function run()
    {
        $isOk = $this->runRegisteredTests()->printSummary();
        if (!$isOk) {
            exit(1);
        }
    }

    public function registerGroup(?string $name, \Closure $fun): self
    {
        $this->registry->registerGroup($name, $fun);
        return $this;
    }

    public function registerTestItem(?string $name, \Closure $fun): self
    {
        $this->registry->registerTestItem($name, $fun);
        return $this;
    }

    public function registerHook(TestHook $hook): self
    {
        $this->registry->registerHook($hook);
        return $this;
    }

    public function registerAllTestFiles(): self
    {
        $mainFile = $this->config->mainFile();
        if (\file_exists($mainFile)) {
            require_once $mainFile;
        }

        $testsDir = $this->config->testsDir();
        $testFiles = Path::getFilesRecursive(
            $testsDir,
            function ($path) {
                return Path::isTestCaseFile($path);
            }
        );

        foreach ($testFiles as $testFile) {
            $this->registerTestFile($testFile);
        }

        return $this;
    }

    public function registerTestFile(string $path): self
    {
        $this->registry->registerTestFile($path);
        return $this;
    }

    public function runRegisteredTests(): self
    {
        $this->runner
             ->setFiles($this->registry->getTestFiles())
             ->run();
        return $this;
    }

    /**
     * printSummary
     * @return bool is ok or not
     */
    public function printSummary(): bool
    {
        $stats = $this->stats;

        $okTests = $stats->getOkTests();
        $errTests = $stats->getErrTests();
        $allTests = $okTests + $errTests;

        $okAsserts = $stats->getOkAsserts();
        $errAsserts = $stats->getErrAsserts();
        $allAsserts = $okAsserts + $errAsserts;

        $isOk = $errTests === 0 && $errAsserts === 0;

        if ($isOk) {
            Printer::success('Summary:');
            $summary = [
                "  tests: [$okTests / $allTests] - ok;",
                "asserts: [$okAsserts / $allAsserts] - ok"
            ];
            Printer::success(implode(' ', $summary));
        } else {
            Printer::error('Summary:');
            $summary = [
                "  tests: [$okTests / $allTests] - $errTests failed;",
                "asserts: [$okAsserts / $allAsserts] - $errAsserts failed",
            ];
            Printer::error(implode(' ', $summary));
        }

        return $isOk;
    }
}
