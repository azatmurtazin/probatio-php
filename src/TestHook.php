<?php

declare(strict_types=1);

namespace Probatio;

use RuntimeException;

class TestHook implements Runnable
{
    public const BEFORE_ALL  = 'before_all';
    public const AFTER_ALL   = 'after_all';
    public const BEFORE_EACH = 'before_each';
    public const AFTER_EACH  = 'after_each';
    public const ALLOWED_TYPES = [
        self::BEFORE_ALL,
        self::AFTER_ALL,
        self::BEFORE_EACH,
        self::AFTER_EACH,
    ];

    /** @var string */
    protected $type;

    /** @var \Closure */
    protected $fun;

    /** @var CodeLoc */
    protected $loc;

    public function __construct(string $type, \Closure $fun)
    {
        if (!\in_array($type, self::ALLOWED_TYPES)) {
            throw new RuntimeException("Not allowed hook type: $type");
        }

        $this->type = $type;
        $this->fun = $fun;
        $this->loc = CodeLoc::fromFun($fun);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function run(TestCase $tc)
    {
        try {
            $fun = $this->fun->bindTo($tc, $tc);
            $fun();
        } catch (\Throwable $e) {
            Printer::noticeErr("failed to run {$this->type} hook");
        }
    }
}
