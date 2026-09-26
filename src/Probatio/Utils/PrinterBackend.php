<?php

declare(strict_types=1);

namespace Probatio\Utils;

class PrinterBackend
{
    /** @var int */
    protected $indentation = 0;

    /** @var int */
    protected $verbosity;

    /** @var resource|string */
    protected $res;

    /** @var bool */
    protected $showTime = false;

    public const VERBOSITY_BRIEF = 0;
    public const VERBOSITY_NORMAL = 1;
    public const VERBOSITY_VERBOSE = 2;

    public const LEVEL_ERROR = 'E';
    public const LEVEL_WARN = 'W';
    public const LEVEL_INFO = 'I';
    public const LEVEL_NOTICE = 'N';
    public const LEVEL_DEBUG = 'D';

    public const OUTPUT_NONE = 0;
    public const OUTPUT_SHORT = 1;
    public const OUTPUT_FULL = 0;

    public const PRINT_MAP = [
        self::LEVEL_ERROR  => [
            self::VERBOSITY_BRIEF   => self::OUTPUT_FULL,
            self::VERBOSITY_NORMAL  => self::OUTPUT_FULL,
            self::VERBOSITY_VERBOSE => self::OUTPUT_FULL,
        ],
        self::LEVEL_WARN   => [
            self::VERBOSITY_BRIEF   => self::OUTPUT_FULL,
            self::VERBOSITY_NORMAL  => self::OUTPUT_FULL,
            self::VERBOSITY_VERBOSE => self::OUTPUT_FULL,
        ],
        self::LEVEL_INFO   => [
            self::VERBOSITY_BRIEF   => self::OUTPUT_FULL,
            self::VERBOSITY_NORMAL  => self::OUTPUT_FULL,
            self::VERBOSITY_VERBOSE => self::OUTPUT_FULL,
        ],
        self::LEVEL_NOTICE => [
            self::VERBOSITY_BRIEF   => self::OUTPUT_SHORT,
            self::VERBOSITY_NORMAL  => self::OUTPUT_FULL,
            self::VERBOSITY_VERBOSE => self::OUTPUT_FULL,
        ],
        self::LEVEL_DEBUG  => [
            self::VERBOSITY_BRIEF   => self::OUTPUT_NONE,
            self::VERBOSITY_NORMAL  => self::OUTPUT_NONE,
            self::VERBOSITY_VERBOSE => self::OUTPUT_FULL,
        ],
    ];

    /**
     * Constructor
     * @param resource|string $res
     */
    public function __construct($res = STDOUT, $verbosity = 0, bool $showTime = false)
    {
        $this->res = $res;
        $this->verbosity = $verbosity;
        $this->showTime = $showTime;
    }

    public function print(string $level = self::LEVEL_INFO, ?string $bullet = null, string $msg = '')
    {
        $outputMode = self::PRINT_MAP[$level][$this->verbosity];
        switch ($outputMode) {
            case self::OUTPUT_FULL:
                $now = $this->showTime ? self::now() . ' ' : '';
                $padding = \str_repeat('  ', $this->indentation);
                $bullet = $bullet ? "$bullet " : '';
                $msg = "{$now}[{$level}] {$padding}{$bullet}{$msg}\n";
                self::write($msg);
                break;
            case self::OUTPUT_SHORT:
                self::write($bullet ?? $level);
                break;
            case self::OUTPUT_NONE:
                break;
        }
    }

    public static function now(): string
    {
        [$usec, $sec] = explode(' ', microtime());
        return date('Y-m-d H:i:s', (int) $sec) . '.' . $usec;
    }

    public function write(string $msg)
    {
        if (\is_resource($this->res)) {
            \fwrite($this->res, $msg);
        } elseif (\is_string($this->res)) {
            \file_put_contents($this->res, $msg, FILE_APPEND | LOCK_EX);
        }
    }

    public function resetIndentation()
    {
        $this->indentation = 0;
    }

    public function incIndentation()
    {
        $this->indentation++;
    }

    public function decIndentation()
    {
        $this->indentation--;
    }
}
