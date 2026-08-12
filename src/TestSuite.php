<?php

declare(strict_types=1);

namespace Epreuve;

class TestSuite
{
    protected static $instance = null;

    protected string $mainFile = "";
    protected array $cliArgs = [];
    protected string $path = "";
    /** @var TestCase[] */
    protected array $testCases = [];
    protected array $oks = [];
    protected array $errors = [];

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

    public function maybeRunAllTests(): void
    {
        if ($this->path === $this->mainFile) {
            $this->runAllTests();
        }
    }

    public function register(?string $name, callable $fun): self
    {
        [$file, $line] = Utils::getCaller();
        $opts = ["file" => $file, "line" => $line];
        if ($name !== null) {
            $opts["name"] = $name;
        }
        $tc = new TestCase($opts);
        $fun($tc);
        $this->testCases[] = $tc;

        return $this;
    }

    public function registerAllTests(): self
    {
        return $this;
    }

    public function runAllTests(): self
    {
        echo "run all tests\n\n";

        $dir = \dirname($this->mainFile);
        $testFiles = $this->getFilesRecursive($dir);

        foreach ($testFiles as $testFile) {
            (function() use ($testFile) {
                require_once $testFile;
            })();
        }

        $tcKeys = array_keys($this->testCases);
        shuffle($tcKeys);

        foreach ($tcKeys as $tcKey) {
            $this->testCases[$tcKey]->run();
        }

        $oks = array_sum(array_map(fn(TestCase $tc) => $tc->getOkCounter(), $this->testCases));
        $all = array_sum(array_map(fn(TestCase $tc) => $tc->getCounter(), $this->testCases));

        if ($oks === $all) {
            echo "✅ summary: [$oks / $all] - all tests are ok\n";
        } else {
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

    public function maybeRunOneTest(): void
    {
        [$file, $_line] = Utils::getCaller();
        if ($this->path === $file) {
            $this->runIndividualTest($file);
        }
    }
}
