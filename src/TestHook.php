<?php

declare(strict_types=1);

namespace Probatio;

use RuntimeException;

class TestHook implements ITestNode
{
    public const BEFORE_ALL  = "before_all";
    public const AFTER_ALL   = "after_all";
    public const BEFORE_EACH = "before_each";
    public const AFTER_EACH  = "after_each";
    public const ALLOWED_TYPES = [
        self::BEFORE_ALL,
        self::AFTER_ALL,
        self::BEFORE_EACH,
        self::AFTER_EACH,
    ];

    protected $type;
    protected $fun;
    protected $caller;

    public function __construct(string $type, \Closure $fun, Caller $caller)
    {
        if (!\in_array($type, self::ALLOWED_TYPES)) {
            throw new RuntimeException("Not allowed hook type: $type");
        }

        $this->type = $type;
        $this->fun = $fun;
        $this->caller = $caller;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function run(TestCase $tc)
    {
        $fun = $this->fun->bindTo($tc, $tc);
        $fun();
    }
}
