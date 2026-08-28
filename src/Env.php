<?php

declare(strict_types=1);

namespace Probatio;

class Env
{
    public static function getStr(string $name, string $default = ""): string
    {
        $value = \getenv($name);
        $value = ($value !== false) ? $value : $default;
        return $value;
    }
}
