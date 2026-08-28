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
    public static function fromFun(?\Closure $fun)
    {
        if ($fun === null) {
            return null;
        }

        $ref = new \ReflectionFunction($fun);
        $file = $ref->getFileName();
        $file = Utils::maybeRemoveCwd($file);
        $start = $ref->getStartLine();
        $end = $ref->getEndLine();

        return new self($file, $start, $end);
    }

    public function __construct(string $file, int $start, int $end)
    {
        $this->file = $file;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * toArray() - converts location object to array [$file, $start, $end]
     * @return array{string, int, int}
     */
    public function toArray(): array
    {
        return [$this->file, $this->start, $this->end];
    }
}
