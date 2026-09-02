<?php

declare(strict_types=1);

namespace Probatio;

use Probatio\Runners\SuiteRunner;

class Expectation
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function toBe($expected): self
    {
        $runner = SuiteRunner::getInstance();
        $tc = $runner->getCurrentCase() ?? new TestCase();
        $tc->assertEq($expected, $this->value);
        return $this;
    }
}
