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
        $registry = TestSuite::getInstance()->getRegistry();
        $tc = $registry->getCurrentCase();
        $tc->assertEq($expected, $this->value);
        return $this;
    }
}
