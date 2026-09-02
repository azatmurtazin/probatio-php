<?php

declare(strict_types=1);

namespace Probatio;

class CodeLoc
{
    /** @var ?string */
    protected $file;

    /** @var ?int */
    protected $start;

    /** @var ?int */
    protected $end;

    /**
     * fromFun() - builds location object from $fun or returns null
     * @param ?\Closure $fun
     * @return CodeLoc|null
     */
    public static function fromFun(?\Closure $fun): self
    {
        if ($fun === null) {
            return new self();
        }

        $ref = new \ReflectionFunction($fun);
        $file = $ref->getFileName();
        $start = $ref->getStartLine();
        $end = $ref->getEndLine();

        return new self($file, $start, $end);
    }

    /**
     * fromCaller() - builds location from the stack trace
     * @param int $level
     * @return CodeLoc
     */
    public static function fromCaller(int $level = 1): self
    {
        $caller = new Caller($level);
        [$file, $line] = $caller->toArray();
        return new self($file, $line);
    }

    public static function fromException(\Throwable $ex): self
    {
        $trace = $ex->getTrace();

        foreach ($trace as $row) {
            $file = $row['file'] ?? null;
            $line = $row['line'] ?? null;
            if ($file && !Utils::endsWithIn($file, Caller::EXCLUDE_FILES)) {
                return new self($file, $line);
            }
        }

        return new self();
    }

    public function __construct(?string $file = null, ?int $start = null, ?int $end = null)
    {
        $this->file = Utils::maybeRemoveCwd($file);
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * toArray() - converts location object to array [$file, $start, $end]
     * @return array{?string, ?int, ?int}
     */
    public function toArray(): array
    {
        return [$this->file, $this->start, $this->end];
    }
}
