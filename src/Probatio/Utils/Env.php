<?php

declare(strict_types=1);

namespace Probatio\Utils;

class Env
{
    public static function getStr(string $name, string $default = ''): string
    {
        $value = \getenv($name);
        $value = ($value !== false) ? $value : $default;
        return $value;
    }

    public static function getBool(string $name, $default = false): bool
    {
        $val = getenv($name);
        $val = ($val === false) ? $default : $val;
        return \filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }
}
