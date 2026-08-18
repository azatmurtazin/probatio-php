<?php

declare(strict_types=1);

namespace Probatio;

class Caller
{
    public const GLOBAL_HELPERS_FILE = "src/global_helpers.php";

    protected $lvl;
    protected $file;
    protected $line;

    public function __construct($lvl = 1, $file = null, $line = null)
    {
        if ($file === null && $line === null) {
            $trace = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $lvl + 2);
            if (isset($trace[$lvl])) {
                $file = $trace[$lvl]["file"] ?? null;
                $file = Utils::maybeRemoveCwd($file);
                $line = $trace[$lvl]["line"] ?? null;

                if (Utils::endsWith($file, self::GLOBAL_HELPERS_FILE)) {
                    $lvl++;
                    if (isset($trace[$lvl])) {
                        $file = $trace[$lvl]["file"] ?? null;
                        $file = Utils::maybeRemoveCwd($file);
                        $line = $trace[$lvl]["line"] ?? null;
                    }
                }
            }
        }

        $this->lvl = $lvl;
        $this->file = $file;
        $this->line = $line;
    }

    /**
     * fl
     * @return array{string|null, int|null, int}
     */
    public function fl(): array
    {
        return [$this->file, $this->line, $this->lvl];
    }
}
