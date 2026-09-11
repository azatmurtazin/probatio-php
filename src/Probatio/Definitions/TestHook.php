<?php

declare(strict_types=1);

namespace Probatio\Definitions;

use Probatio\Runners\RunnerException;
use Probatio\Utils\Location;
use Probatio\Utils\Printer;

class TestHook
{
    public const BEFORE_ALL  = 'before_all';
    public const AFTER_ALL   = 'after_all';
    public const BEFORE_EACH = 'before_each';
    public const AFTER_EACH  = 'after_each';
    public const LET         = 'let';
    public const SET         = 'set';
    public const ALLOWED_TYPES = [
        self::BEFORE_ALL,
        self::AFTER_ALL,
        self::BEFORE_EACH,
        self::AFTER_EACH,
        self::LET,
        self::SET,
    ];

    /** @var string */
    protected $type;

    /** @var \Closure */
    protected $fun;

    /** @var ?string */
    protected $name;

    /** @var Location */
    protected $loc;

    public function __construct(string $type, \Closure $fun, $name = null)
    {
        if (!\in_array($type, self::ALLOWED_TYPES)) {
            throw new \RuntimeException("Not allowed hook type: $type");
        }

        $this->type = $type;
        $this->fun = $fun;
        $this->name = $name;
        $this->loc = Location::fromFun($fun);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getFun(): \Closure
    {
        return $this->fun;
    }

    public function getLoc(): Location
    {
        return $this->loc;
    }
}
