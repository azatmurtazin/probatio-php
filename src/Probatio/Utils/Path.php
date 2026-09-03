<?php

declare(strict_types=1);

namespace Probatio\Utils;

use Probatio\Utils;

class Path
{
    public static function isTestCaseFile(string $path): bool
    {
        return \is_file($path) && \preg_match("/(\w+(_test|Test)(s?))\.php$/", $path);
    }

    public static function maybeRemoveCwd(?string $path): ?string
    {
        $cwd = getcwd() . '/';
        if ($path !== null && Str::startsWith($path, $cwd)) {
            $path = \substr($path, \strlen($cwd));
        }
        return $path;
    }

    public static function getFilesRecursive(string $dir, ?\Closure $fun = null): array
    {
        if (!is_dir($dir)) {
            Printer::warn("'$dir' is not a directory");
            return [];
        }

        $fileList = [];
        $directory = new \RecursiveDirectoryIterator($dir);
        $iterator = new \RecursiveIteratorIterator($directory);
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getPathname();
                $ok = ($fun === null) || ($fun instanceof \Closure && $fun($filePath));
                if ($ok) {
                    $fileList[] = $filePath;
                }
            }
        }
        return $fileList;
    }
}
