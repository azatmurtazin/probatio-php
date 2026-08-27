<?php

declare(strict_types=1);

namespace ExampleApp;

class Calculator
{
    public static function add($x, $y)
    {
        return $x + $y;
    }

    public static function sub($x, $y)
    {
        return $x - $y;
    }

    public static function mul($x, $y)
    {
        return $x * $y;
    }

    public static function div($x, $y)
    {
        if ($y == 0) {
            return INF;
        }
        return $x / $y;
    }
}
