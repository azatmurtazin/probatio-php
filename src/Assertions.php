<?php

declare(strict_types=1);

namespace Probatio;

trait Assertions
{
    public function assertEq($expected, $actual)
    {
        if ($actual === $expected) {
            echo "    ✅ assertion is ok\n";
            return;
        }

        $ac = var_export($actual, true);
        $ex = var_export($expected, true);
        $caller = new Caller();
        [$file, $line] = $caller->toArray();
        throw new AssertionError("$ac is not equal to $ex ($file:$line)");
    }

    public function assertTrue($value)
    {
        if ($value === true) {
            echo "    ✅ assertion is ok\n";
            return;
        }

        $val = var_export($value, true);
        $caller = new Caller();
        [$file, $line] = $caller->toArray();
        throw new AssertionError("$val is not true ($file:$line)");
    }
}
