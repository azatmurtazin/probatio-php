<?php

declare(strict_types=1);

namespace Probatio\Utils;

class Printer
{
    public const BULLET_OK = '✅';
    public const BULLET_ERR = '❌';
    public const BULLET_WARN = '⚠️';
    public const BULLET_INFO = 'ℹ️';
    public const BULLET_DEBUG = '🪲';
    public const BULLET_FILE = '📄';
    public const BULLET_GROUP = '📦';
    public const BULLET_ITEM = '🔹';

    /** @var array<string, string> */
    public const BULLETS = [
        'ok'    => self::BULLET_OK,
        'err'   => self::BULLET_ERR,
        'warn'  => self::BULLET_WARN,
        'info'  => self::BULLET_INFO,
        'debug' => self::BULLET_DEBUG,
        'file'  => self::BULLET_FILE,
        'group' => self::BULLET_GROUP,
        'item'  => self::BULLET_ITEM,
    ];

    public const VERBOSITY_BRIEF = 0;
    public const VERBOSITY_NORMAL = 1;
    public const VERBOSITY_VERBOSE = 2;

    public const VERBOSITY_LEVELS = [
        self::VERBOSITY_BRIEF,
        self::VERBOSITY_NORMAL,
        self::VERBOSITY_VERBOSE,
    ];

    /** @var int */
    protected static $level = 0;

    /** @var int */
    protected static $verbosity = 0;

    public static function bullet(string $b): string
    {
        return self::BULLETS[$b] ?? "[$b]";
    }

    public static function success(string $msg = '')
    {
        self::println($msg, 'ok');
    }

    public static function error(string $msg = '')
    {
        self::println($msg, 'err');
    }

    public static function warn(string $msg = '')
    {
        self::println($msg, 'warn');
    }

    public static function info(string $msg = '')
    {
        self::println($msg, 'info');
    }

    public static function debug(string $msg = '', ?string $b = null)
    {
        if (self::$verbosity === self::VERBOSITY_VERBOSE) {
            self::println($msg, $b);
        }
    }

    public static function notice(string $msg = '', ?string $b = null)
    {
        if (self::$verbosity === self::VERBOSITY_BRIEF) {
            self::print(self::bullet($b), null, false);
        } else {
            self::println($msg, $b);
        }
    }

    public static function noticeOk(string $msg = '')
    {
        self::notice($msg, 'ok');
    }

    public static function noticeErr(string $msg = '')
    {
        self::notice($msg, 'err');
    }

    public static function noticeFile(string $msg = '')
    {
        self::notice($msg, 'file');
    }

    public static function noticeGroup(string $msg = '')
    {
        self::notice($msg, 'group');
    }

    public static function noticeItem(string $msg = '')
    {
        self::notice($msg, 'item');
    }

    public static function println(string $msg = '', ?string $b = null)
    {
        self::print("$msg\n", $b);
    }


    public static function print(string $msg = '', ?string $b = null, bool $hasPadding = true)
    {
        $padding = $hasPadding ? self::getPadding() : '';
        $msg = ($b !== null) ? self::bullet($b) . " $msg" : $msg;
        \fwrite(STDOUT, "{$padding}{$msg}");
    }

    public static function getPadding(): string
    {
        return \str_repeat('  ', self::getLevel());
    }

    public static function getLevel(): int
    {
        return self::$level;
    }

    public static function resetLevel()
    {
        self::$level = 0;
    }

    public static function incLevel()
    {
        self::$level++;
    }

    public static function decLevel()
    {
        self::$level--;
    }

    public static function setVerbosity(int $verbosity)
    {
        if (\in_array($verbosity, self::VERBOSITY_LEVELS)) {
            self::$verbosity = $verbosity;
        }
    }
}
