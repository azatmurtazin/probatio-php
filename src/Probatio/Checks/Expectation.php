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
            return (new static($this->value))->invert();
        }

        throw new \RuntimeException('Undefined property: ' . __CLASS__ . "::${$name}");
    }

    public function invert(): self
    {
        $this->inverted = !$this->inverted;
        return $this;
    }

    public function toBe($expected): self
    {
        $tc = $this->tc();
        $this->inverted
            ? $tc->assertNotSame($expected, $this->value)
            : $tc->assertSame($expected, $this->value);
        return $this;
    }

    public function toBeBetween($min, $max): self
    {
        $tc = $this->tc();
        $this->inverted
            ? $tc->assertNotBetween($this->value, $min, $max)
            : $tc->assertBetween($this->value, $min, $max);
        return $this;
    }

    public function toBeEmpty(): self
    {
        $tc = $this->tc();
        $this->inverted
            ? $tc->assertNotEmpty($this->value)
            : $tc->assertEmpty($this->value);
        return $this;
    }

    public function toBeTrue(): self
    {
        $tc = $this->tc();
        $this->inverted
            ? $tc->assertNotTrue($this->value)
            : $tc->assertTrue($this->value);
        return $this;
    }

    public function toBeTruthy(): self
    {
        $tc = $this->tc();
        $this->inverted
            ? $tc->assertNotTruthy($this->value)
            : $tc->assertTruthy($this->value);
        return $this;
    }

    public function toBeFalse(): self
    {
        $tc = $this->tc();
        $this->inverted
            ? $tc->assertNotFalse($this->value)
            : $tc->assertFalse($this->value);
        return $this;
    }

    public function toBeFalsy(): self
    {
        $tc = $this->tc();
        $this->inverted
            ? $tc->assertNotFalsy($this->value)
            : $tc->assertFalsy($this->value);
        return $this;
    }

    protected function tc(): TestCase
    {
        return probatio()->runner()->getCurrentCase() ?? new TestCase();
    }
}
