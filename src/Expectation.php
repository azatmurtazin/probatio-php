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
        $runner = TestRunner::getInstance();
        $tc = $runner->getCurrentCase() ?? new TestCase();
        $tc->assertEq($expected, $this->value);
        return $this;
    }
}
