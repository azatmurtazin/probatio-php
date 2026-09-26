<?php

declare(strict_types=1);

namespace Probatio\Utils;

class Printer
{
    public const LEVEL_ERROR = 'E';
    public const LEVEL_WARN = 'W';
    public const LEVEL_INFO = 'I';
    public const LEVEL_NOTICE = 'N';
    public const LEVEL_DEBUG = 'D';

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
    protected static $verbosity = 0;

    /** @var PrinterBackend[] */
    protected static $backends = [];

    public static function addBackend(PrinterBackend $backend)
    {
        self::$backends[] = $backend;
    }

    public static function bullet(string $b): string
    {
        return self::BULLETS[$b] ?? "[$b]";
    }

    public static function success(string $msg = '')
    {
        self::print(self::LEVEL_INFO, 'ok', $msg);
    }

    public static function error(string $msg = '')
    {
        self::print(self::LEVEL_ERROR, 'err', $msg);
    }

    public static function warn(string $msg = '')
    {
        self::print(self::LEVEL_WARN, 'warn', $msg);
    }

    public static function info(string $msg = '')
    {
        self::print(self::LEVEL_INFO, 'info', $msg);
    }

    public static function debug(string $msg = '', ?string $b = null)
    {
        self::print(self::LEVEL_DEBUG, 'debug', $msg);
    }

    public static function notice(string $msg = '', ?string $b = null)
    {
        self::print(self::LEVEL_NOTICE, $b, $msg);
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

    public static function breakLine()
    {
        self::write("\n");
    }

    public static function print(string $level, ?string $bullet, string $msg)
    {
        $bullet = $bullet ? self::bullet($bullet) : null;
        foreach (self::$backends as $backend) {
            $backend->print($level, $bullet, $msg);
        }
    }

    public static function write(string $msg)
    {
        foreach (self::$backends as $backend) {
            $backend->write($msg);
        }
    }

    public static function resetIndentation()
    {
        foreach (self::$backends as $backend) {
            $backend->resetIndentation();
        }
    }

    public static function incIndentation()
    {
        foreach (self::$backends as $backend) {
            $backend->incIndentation();
        }
    }

    public static function decIndentation()
    {
        foreach (self::$backends as $backend) {
            $backend->decIndentation();
        }
    }
}
