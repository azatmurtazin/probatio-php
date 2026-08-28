<?php

declare(strict_types=1);

namespace Probatio;

class Caller
{
    public const GLOBAL_FUNCTIONS_FILE = "src/GlobalFunctions.php";

    protected $level;
    protected $file;
    protected $line;

    public function __construct($level = 1, $file = null, $line = null)
    {
        if ($file === null && $line === null) {
            $trace = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $level + 2);
            if (isset($trace[$level])) {
                $file = $trace[$level]["file"] ?? null;
                $file = Utils::maybeRemoveCwd($file);
                $line = $trace[$level]["line"] ?? null;

                if (Utils::endsWith($file, self::GLOBAL_FUNCTIONS_FILE)) {
                    $level++;
                    if (isset($trace[$level])) {
                        $file = $trace[$level]["file"] ?? null;
                        $file = Utils::maybeRemoveCwd($file);
                        $line = $trace[$level]["line"] ?? null;
                    }
                }
            }
        }

        $this->level = $level;
        $this->file = $file;
        $this->line = $line;
    }

    /**
     * toArray() - converts caller object to array [$file, $line, $level]
     * @return array{?string, ?int, int}
     */
    public function toArray(): array
    {
        return [$this->file, $this->line, $this->level];
    }
}
