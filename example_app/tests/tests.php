<?php

declare(strict_types=1);

require_once __DIR__."/../../vendor/autoload.php";

/**
 * getCaller
 * @return array{string|null, string|null}
 */
function getCaller(): array
{
    $caller = [null, null];
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    if (isset($trace[1])) {
        $file = $trace[1]["file"] ?? null;
        $file = maybeRemoveCwd($file);
        $line = $trace[1]["line"] ?? null;
        $caller = [$file, $line];
    }
    return $caller;
}

function maybeRemoveCwd(?string $path): ?string
{
    $cwd = getcwd();
    if ($path !== null && str_starts_with($path, $cwd)) {
        $path = ".".substr($path, strlen($cwd));
    }
    return $path;
}

class TestSuite
{
    protected static $instance = null;

    protected array $cliArgs = [];
    protected string $path = "";
    /** @var TestCase[] */
    protected array $testCases = [];

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
        $this->path = maybeRemoveCwd($this->path);
    }

    public function maybeRunAllTests(): void
    {
        echo "maybe run all tests...\n";
        $file = maybeRemoveCwd(__FILE__);
        if ($this->path === $file) {
            echo "yep\n";
            $this->runAllTests();
        } else {
            echo "nope\n";
        }
    }

    public function register(?string $name, callable $fun): self
    {
        [$file, $line] = getCaller();
        echo "register test case...\n";
        echo "  from $file:$line\n";
        $opts = ["file" => $file, "line" => $line];
        if ($name !== null) {
            $opts["name"] = $name;
        }
        echo "# test case opts: ".var_export($opts, true)."\n";
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
        echo "run all tests\n";
        $testFiles = $this->getFilesRecursive(__DIR__);
        var_dump($testFiles);

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

        return $this;
    }

    public function getFilesRecursive(string $path): array
    {
        $fileList = [];
        $directory = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($directory);
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getPathname();
                if (str_ends_with($filePath, "_test.php")) {
                    $fileList[] = $file->getPathname();
                }
            }
        }
        return $fileList;
    }

    public function runIndividualTest(string $path): self
    {
        echo "run test case: $path\n";
        foreach ($this->testCases as $testCase) {
            echo "# check file: ".var_export($testCase->getFile(), true)."\n";
            if ($path === $testCase->getFile()) {
                $testCase->run();
            }
        }
        return $this;
    }

    public function maybeRunOneTest(): void
    {
        [$file, $_line] = getCaller();
        echo "maybe run one test...\n";
        if ($this->path === $file) {
            echo "yep\n";
            $this->runIndividualTest($file);
        } else {
            echo "nope\n";
        }
    }
}

class TestCase
{
    protected array $opts = [];
    protected array $assigns = [];
    /** @var TestItem[] */
    protected array $testItems = [];
    protected mixed $beforeFun = null;
    protected mixed $afterFun = null;

    public function __construct(array $opts = [])
    {
        $this->opts = $opts;
    }

    public function getName(): ?string
    {
        return $this->opts["name"] ?? null;
    }

    public function getFile(): ?string
    {
        return $this->opts["file"] ?? null;
    }

    public function getLine(): ?int
    {
        return $this->opts["line"] ?? null;
    }

    public function run(): self
    {
        $name = $this->getName();
        $file = $this->getFile();
        $line = $this->getLine();
        echo "running case: $name ($file:$line) ...\n";
        $this->execBefore();
        $tiKeys = array_keys($this->testItems);
        shuffle($tiKeys);
        foreach ($tiKeys as $tiKey) {
            $this->testItems[$tiKey]->run();
        }
        $this->execAfter();
        return $this;
    }

    public function before(callable $fun): self
    {
        echo "register before\n";
        $this->beforeFun = $fun;
        return $this;
    }

    public function after(callable $fun): self
    {
        echo "register after\n";
        $this->afterFun = $fun;
        return $this;
    }

    public function test(?string $name, callable $fun): self
    {
        echo "register test item\n";
        [$file, $line] = getCaller();
        echo "  from $file:$line\n";
        $opts = ["file" => $file, "line" => $line];
        if ($name !== null) {
            $opts["name"] = $name;
        }
        $ti = new TestItem($this, $fun, $opts);
        $this->testItems[] = $ti;
        return $this;
    }

    public function get(string $key): mixed
    {
        return $this->assigns[$key];
    }

    public function set(string $key, mixed $value): self
    {
        $this->assigns[$key] = $value;
        return $this;
    }

    public function unset(string $key): self
    {
        unset($this->assigns[$key]);
        return $this;
    }

    public function assertEq($expected, $actual) {
        if ($expected === $actual) {
            echo "assertion is ok\n";
        } else {
            throw new \RuntimeException("assertion failed, not equal");
        }
    }

    public function execBefore()
    {
        if (is_callable($this->beforeFun)) {
            echo "running before\n";
            $beforeFun = $this->beforeFun;
            $beforeFun($this);
        }
        return $this;
    }

    public function execAfter()
    {
        if (is_callable($this->afterFun)) {
            echo "running after\n";
            $afterFun = $this->afterFun;
            $afterFun($this);
        }
        return $this;
    }
}

class TestItem
{
    protected TestCase $tc;
    protected mixed $fun;
    protected array $opts = [];

    public function __construct(TestCase $tc, callable $fun, array $opts = [])
    {
        $this->tc = $tc;
        $this->fun = $fun;
        $this->opts = $opts;
    }

    public function run()
    {
        $name = $this->opts["name"] ?? null;
        $file = $this->opts["file"] ?? null;
        $line = $this->opts["line"] ?? null;
        echo "running item: $name ($file:$line)\n";
        try {
            $fun = $this->fun;
            $fun($this->tc);
            echo "✅ ok\n";
        } catch(\Exception $e) {
            echo "❌ error: ".$e->getMessage()."\n";
        }
    }
}

TestSuite::getInstance()->maybeRunAllTests();
