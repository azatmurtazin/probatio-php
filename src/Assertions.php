<?php

declare(strict_types=1);

namespace Epreuve;

trait Assertions
{
    public function assertEq($expected, $actual) {
        if ($expected === $actual) {
            echo "    * assertion is ok\n";
        } else {
            throw new \RuntimeException("assertion failed, not equal");
        }
    }
}
