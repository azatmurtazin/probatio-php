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
    /** @var TestCase[] */
    protected $testCases = [];
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

    public function registerTests(): self
    {
        if (empty($this->tcFiles)) {
            echo "run all tests\n\n";
            $this->registerAllTests();
        } else {
            echo "run test cases: ".implode(", ", $this->tcFiles)."\n\n";
            foreach ($this->tcFiles as $tcFile) {
                $this->registerTestCase($tcFile);
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

    public function describe(?string $name, \Closure $fun, ?Caller $caller = null): self
    {
        $caller = $caller ?? new Caller();
        [$file, $line] = $caller->fl();
        $opts = ["name" => $name, "file" => $file, "line" => $line];
        $tc = new TestCase($opts);
        $fun($tc);
        $this->testCases[] = $tc;

        return $this;
    }

    public function registerAllTests(): self
    {
        $dir = \dirname($this->mainFile);
        $testFiles = $this->getFilesRecursive($dir);

        foreach ($testFiles as $testFile) {
            (function () use ($testFile) {
                require_once $testFile;
            })();
        }

        return $this;
    }

    public function registerTestCase(string $path): self
    {
        (function () use ($path) {
            require_once $path;
        })();

        return $this;
    }

    public function runRegisteredTests(): self
    {
        $tcKeys = array_keys($this->testCases);
        shuffle($tcKeys);

        foreach ($tcKeys as $tcKey) {
            $this->testCases[$tcKey]->run();
        }

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

        $oks = array_sum(array_map($okCntFun, $this->testCases));
        $all = array_sum(array_map($cntFun, $this->testCases));

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
        $tcKeys = array_keys($this->testCases);
        shuffle($tcKeys);
        foreach ($tcKeys as $tcKey) {
            $tc = $this->testCases[$tcKey];
            if ($path === $tc->getFile()) {
                $tc->run();
            }
        }
        return $this;
    }
}
