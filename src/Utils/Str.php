<?php

declare(strict_types=1);

namespace Probatio\Utils;

class Str
{
    public static function startsWith(?string $haystack, ?string $needle): bool
    {
        if (function_exists('str_starts_with')) {
            return \str_starts_with($haystack, $needle);
        }

        if ($needle === null || $needle === '') {
            return true;
        }
        if ($haystack === null) {
            return false;
        }
        return \strncmp($haystack, $needle, \strlen($needle)) === 0;
    }

    public static function endsWithIn(?string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            $res = self::endsWith($haystack, $needle);
            if ($res) {
                return true;
            }
        }
        return false;
    }

    public static function endsWith(?string $haystack, ?string $needle): bool
    {
        if (function_exists('str_ends_with')) {
            return \str_ends_with($haystack, $needle);
        }

        if ($needle === null || $needle === '') {
            return true;
        }
        if ($haystack === null) {
            return false;
        }

        $needleLength = \strlen($needle);
        if ($needleLength > \strlen($haystack)) {
            return false;
        }

        return substr_compare($haystack, $needle, -$needleLength, $needleLength) === 0;
    }
}
