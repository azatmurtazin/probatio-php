<?php

declare(strict_types=1);

namespace Probatio\Checks;

use Probatio\Definitions\TestCase;

use function Probatio\Functions\probatio;

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
        $tc = probatio()->runner()->getCurrentCase() ?? new TestCase();
        $tc->assertEq($expected, $this->value);
        return $this;
    }
}
