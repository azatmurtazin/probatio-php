<?php

declare(strict_types=1);

namespace Probatio;

class Utils
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

    public static function maybeRemoveCwd(?string $path): ?string
    {
        $cwd = getcwd() . '/';
        if ($path !== null && self::startsWith($path, $cwd)) {
            $path = \substr($path, \strlen($cwd));
        }
        return $path;
    }

    public static function getTitle($name, $file, $line): string
    {
        return $name !== null ? "$name ($file:$line)" : "$file:$line";
    }
}
