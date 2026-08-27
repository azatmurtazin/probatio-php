<?php

declare(strict_types=1);

namespace Probatio;

class TestSuite
{
    public const BIN_PATH = "vendor/bin/probatio";

    /** @var self|null */
    protected static $instance = null;

    /** @var string */
    protected $mainFile = "";

    /** @var string[] */
    protected $cliArgs = [];

    /** @var string[] */
    protected $tcFiles = [];

    /** @var TestRegistry */
    protected $registry;

    /** @var array */
    protected $oks = [];

    /** @var array */
    protected $errors = [];

    protected $ok = true;

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
            if (Utils::isTestCaseFile($cliArg)) {
                $this->tcFiles[] = $cliArg;
            }
        }
        $this->registry = new TestRegistry();
    }

    public function setMainFile(string $mainFile): self
    {
        $this->mainFile = Utils::maybeRemoveCwd($mainFile);
        return $this;
    }

    public function isOk()
    {
        return $this->ok;
    }

    public function registerTestFiles(): self
    {
        if (empty($this->tcFiles)) {
            echo "run all tests\n\n";
            $this->registerAllTestFiles();
        } else {
            echo "run test cases: ".implode(", ", $this->tcFiles)."\n\n";
            foreach ($this->tcFiles as $tcFile) {
                $this->registerTestFile($tcFile);
            };
        }

        return $this;
    }

    public function run()
    {
        $this->runRegisteredTests()->printSummary();
        if (!$this->isOk()) {
            exit(1);
        }
    }

    public function registerGroup(?string $name, \Closure $fun, Caller $caller): self
    {
        $this->registry->registerGroup($name, $fun, $caller);
        return $this;
    }

    public function registerTestItem(?string $name, \Closure $fun, Caller $caller): self
    {
        $this->registry->registerTestItem($name, $fun, $caller);
        return $this;
    }

    public function registerHook(TestHook $hook): self
    {
        $this->registry->registerHook($hook);
        return $this;
    }

    public function registerAllTestFiles(): self
    {
        $dir = \dirname($this->mainFile);
        $testFiles = $this->getFilesRecursive($dir);

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
        $this->registry->runRegisteredTests();
        return $this;
    }

    public function printSummary(): self
    {
        $okCntFun = function (TestCase $tc) {
            return $tc->getOkCounter();
        };
        $cntFun = function (TestCase $tc) {
            return $tc->getCounter();
        };

        $oks = 0; // array_sum(array_map($okCntFun, $this->testCases));
        $all = 0; // array_sum(array_map($cntFun, $this->testCases));

        if ($oks === $all) {
            echo "✅ summary: [$oks / $all] - all tests are ok\n";
        } else {
            $this->ok = false;
            echo "❌ summary: [ $oks / $all ] - some tests failed \n";
        }

        return $this;
    }

    public function getFilesRecursive(string $path): array
    {
        $fileList = [];
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getPathname();
                if (Utils::isTestCaseFile($filePath)) {
                    $fileList[] = $file->getPathname();
                }
            }
        }
        return $fileList;
    }

    public function runIndividualTest(string $path): self
    {
        echo "run test file: $path\n";
        $this->registry->runRegisteredTests();
        // $tcKeys = array_keys($this->testCases);
        // shuffle($tcKeys);
        // foreach ($tcKeys as $tcKey) {
        //     $tc = $this->testCases[$tcKey];
        //     if ($path === $tc->getFile()) {
        //         $tc->run();
        //     }
        // }
        return $this;
    }
}
