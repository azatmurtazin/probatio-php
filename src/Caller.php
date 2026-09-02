<?php

declare(strict_types=1);

namespace Probatio;

class Caller
{
    public const EXCLUDE_FILES = [
        'src/GlobalFunctions.php',
        'src/Expectation.php',
        'src/Assertions.php',
        'src/CodeLoc.php',
    ];

    protected $level;
    protected $file;
    protected $line;

    public function __construct($level = 1)
    {
        $this->level = $level;
        $this->file = null;
        $this->line = null;

        $trace = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $level + \count(self::EXCLUDE_FILES));

        foreach ($trace as $idx => $row) {
            [$file, $line] = $this->traceToFl($row);
            if (!Utils::endsWithIn($file, self::EXCLUDE_FILES)) {
                $this->file = $file;
                $this->line = $line;
                $this->level = $idx;
                return;
            }
        }
    }

    /**
     * toArray() - converts caller object to array [$file, $line, $level]
     * @return array{?string, ?int, ?int}
     */
    public function toArray(): array
    {
        return [$this->file, $this->line, $this->level];
    }

    /**
     * traceToFl()
     * @param array $row
     * @return array{?string, ?int}
     */
    protected function traceToFl(array $row): array
    {
        $file = $row['file'] ?? null;
        $file = Utils::maybeRemoveCwd($file);
        $line = $row['line'] ?? null;
        return [$file, $line];
    }
}
