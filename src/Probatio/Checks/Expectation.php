<?php

declare(strict_types=1);

namespace Probatio\Checks;

use Probatio\Definitions\TestCase;

use function Probatio\Functions\probatio;

class Expectation
{
    protected $value;
    protected $inverted = false;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __get(string $name)
    {
        if ($name === 'not') {
            $this->inverted = !$this->inverted;
            return $this;
        }

        throw new \RuntimeException('Undefined property: ' . __CLASS__ . "::${$name}");
    }

    public function toBe($expected): self
    {
        $tc = $this->getTc();
        $this->inverted
            ? $tc->assertNotSame($expected, $this->value)
            : $tc->assertSame($expected, $this->value);
        return $this;
    }

    public function toBeBetween($min, $max): self
    {
        $tc = $this->getTc();
        $this->inverted
            ? $tc->assertNotBetween($this->value, $min, $max)
            : $tc->assertBetween($this->value, $min, $max);
        return $this;
    }

    protected function getTc(): TestCase
    {
        return probatio()->runner()->getCurrentCase() ?? new TestCase();
    }
}
