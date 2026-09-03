<?php

declare(strict_types=1);

namespace Probatio\Tools;

use Probatio\Utils\Path;
use Probatio\Utils\Str;

class Location
{
    /** @var ?string */
    protected $file;

    /** @var ?int */
    protected $start;

    /** @var ?int */
    protected $end;

    /** @var ?string */
    protected $name = null;

    /**
     * fromFun() - builds location object from $fun or returns null
     * @param ?\Closure $fun
     * @return self|null
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
     * @return self
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
            if ($file && !Str::endsWithIn($file, Caller::EXCLUDE_FILES)) {
                return new self($file, $line);
            }
        }

        return new self();
    }

    public function __construct(?string $file = null, ?int $start = null, ?int $end = null)
    {
        $this->file = Path::maybeRemoveCwd($file);
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

    public function withName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function __toString(): string
    {
        $name = $this->name;
        $file = $this->file;
        $start = $this->start;
        $end = $this->end;

        $lines = $end !== null ? "$start-$end" : (string) $start;
        return $name !== null ? "$name ($file:$lines)" : "$file:$lines";
    }
}
