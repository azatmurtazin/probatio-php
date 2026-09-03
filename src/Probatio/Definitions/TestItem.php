<?php

declare(strict_types=1);

namespace Probatio\Definitions;

use Probatio\Utils\Location;

class TestItem implements Definition
{
    /** @var ?string */
    protected $name = null;

    /** @var \Closure */
    protected $fun;

    /** @var Location */
    protected $loc;

    public function __construct(?string $name, \Closure $fun)
    {
        $this->name = $name;
        $this->fun = $fun;
        $this->loc = Location::fromFun($fun);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLoc(): Location
    {
        return $this->loc;
    }

    public function getFun(): \Closure
    {
        return $this->fun;
    }
}
