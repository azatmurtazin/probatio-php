<?php

declare(strict_types=1);

namespace Probatio;

class TestSuite
{
    /** @var self|null */
    protected static $instance = null;

    /** @var string */
    protected $mainFile = "";
    /** @var string[] */
    protected $cliArgs = [];
    /** @var string */
    protected $path = "";
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

    protected function __construct() {
        global $argv;
        $this->cliArgs = $argv;

        $this->path = realpath($this->cliArgs[0]);
        $this->path = Utils::maybeRemoveCwd($this->path);
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

    public function defineGlobalFunctions(): self
    {
        require_once __DIR__."/helpers.php";
        return $this;
    }

    public function maybeRunAllTests(): void
    {
        $ts = $this;
        \register_shutdown_function(function() use ($ts) {
            $ts->runRegisteredTests()->printSummary();
            if (!$ts->isOk()) {
                exit(1);
            }
        });

        if ($this->path === $this->mainFile) {
            echo "run all tests\n\n";
            $this->registerAllTests();
        }
    }

    public function describe(?string $name, callable $fun, $caller = null): self
    {
        $caller = $caller ?? Utils::getCaller();
        [$file, $line] = $caller;
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
            (function() use ($testFile) {
                require_once $testFile;
            })();
        }

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
        $okCntFun = function(TestCase $tc) { return $tc->getOkCounter(); };
        $cntFun = function(TestCase $tc) { return $tc->getCounter(); };

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
                if (Utils::endsWith($filePath, "_test.php") || Utils::endsWith($filePath, "_tests.php")) {
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
