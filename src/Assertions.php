<?php

declare(strict_types=1);

namespace Probatio;

trait Assertions
{
    public function assertEq($expected, $actual)
    {
        if ($expected == $actual) {
            echo "    * assertion is ok\n";
            return;
        }

        $ac = var_export($actual, true);
        $ex = var_export($expected, true);
        throw new \RuntimeException("assertion failed: $ac is not equal to $ex");
    }
}
