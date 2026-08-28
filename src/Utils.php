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

    public static function getTitle(?string $name, string $file, int $start, ?int $end = null): string
    {
        $lines = $end !== null ? "$start-$end" : (string)$start;
        return $name !== null ? "$name ($file:$lines)" : "$file:$lines";
    }

    public static function isTestCaseFile(string $path): bool
    {
        return is_file($path) && preg_match("/(\w+(_test|Test)(s?))\.php$/", $path);
    }
}
