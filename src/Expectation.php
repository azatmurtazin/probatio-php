<?php

declare(strict_types=1);

namespace Probatio;

class Expectation
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function toBe($expected): self
    {
        if ($this->value === $expected) {
            echo "    ✅ assertion is ok\n";
            return $this;
        }

        $ac = var_export($this->value, true);
        $ex = var_export($expected, true);
        throw new AssertionError("$ac is not equal to $ex");
    }
}
