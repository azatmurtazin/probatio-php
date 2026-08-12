<?php

declare(strict_types=1);

require_once __DIR__."/../../vendor/autoload.php";

if (!function_exists('str_starts_with')) {
    function str_starts_with(?string $haystack, ?string $needle): bool {
        if ($needle === null || $needle === '') {
            return true;
        }
        if ($haystack === null) {
            return false;
        }
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(?string $haystack, ?string $needle): bool {
        if ($needle === null || $needle === '') {
            return true;
        }
        if ($haystack === null) {
            return false;
        }

        $needleLength = strlen($needle);
        if ($needleLength > strlen($haystack)) {
            return false;
        }

        return substr_compare($haystack, $needle, -$needleLength, $needleLength) === 0;
    }
}

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
        $this->path = maybeRemoveCwd($this->path);
    }

    public function maybeRunAllTests(): void
    {
        $file = maybeRemoveCwd(__FILE__);
        if ($this->path === $file) {
            $this->runAllTests();
        }
    }

    public function register(?string $name, callable $fun): self
    {
        [$file, $line] = getCaller();
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

        $testFiles = $this->getFilesRecursive(__DIR__);

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
        echo "summary: [ $oks / $all ]\n";

        if ($oks === $all) {
            echo "✅ all tests are ok\n";
        } else {
            echo "❌ some tests failed\n";
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
                if (str_ends_with($filePath, "_test.php") || str_ends_with($filePath, "_tests.php")) {
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
        [$file, $_line] = getCaller();
        if ($this->path === $file) {
            $this->runIndividualTest($file);
        }
    }
}

trait Assertions
{
    public function assertEq($expected, $actual) {
        if ($expected === $actual) {
            echo "    * assertion is ok\n";
        } else {
            throw new \RuntimeException("assertion failed, not equal");
        }
    }
}

class TestCase
{
    use Assertions;

    protected array $opts = [];
    protected array $assigns = [];
    /** @var TestItem[] */
    protected array $testItems = [];
    protected mixed $beforeFun = null;
    protected mixed $afterFun = null;
    protected int $counter = 0;
    protected int $okCounter = 0;
    /** @var TestItem[] */
    protected array $failures = [];

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

    public function isOk(): bool
    {
        return $this->counter == $this->okCounter;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function counterInc(): void
    {
        $this->counter++;
    }

    public function getOkCounter(): int
    {
        return $this->okCounter;
    }

    public function okCounterInc(): void
    {
        $this->okCounter++;
    }

    public function registerFailure(TestItem $ti): void
    {
        $this->failures[] = $ti;
    }

    public function run(): self
    {
        $name = $this->getName();
        $file = $this->getFile();
        $line = $this->getLine();
        echo "* $name ($file:$line) ...\n";
        $this->execBefore();
        $tiKeys = array_keys($this->testItems);
        shuffle($tiKeys);
        foreach ($tiKeys as $tiKey) {
            $this->testItems[$tiKey]->run();
        }
        $this->execAfter();
        echo "\n";
        return $this;
    }

    public function before(callable $fun): self
    {
        $this->beforeFun = $fun;
        return $this;
    }

    public function after(callable $fun): self
    {
        $this->afterFun = $fun;
        return $this;
    }

    public function test(?string $name, callable $fun): self
    {
        [$file, $line] = getCaller();
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

    public function execBefore()
    {
        if (is_callable($this->beforeFun)) {
            $beforeFun = $this->beforeFun;
            $beforeFun($this);
        }
        return $this;
    }

    public function execAfter()
    {
        if (is_callable($this->afterFun)) {
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
    protected \Exception|null $error = null;

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
        echo "  * $name ($file:$line)\n";
        $this->tc->counterInc();
        try {
            $fun = $this->fun;
            $fun($this->tc);
            echo "  ✅ ok\n";
            $this->tc->okCounterInc();
        } catch(\Exception $e) {
            $this->error = $e;
            $this->tc->registerFailure($this);
            echo "  ❌ error: ".$e->getMessage()."\n";
        }
    }
}

TestSuite::getInstance()->maybeRunAllTests();
