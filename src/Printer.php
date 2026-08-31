<?php

declare(strict_types=1);

namespace Probatio;

class Printer
{
    public static function success(string $msg = "")
    {
        self::println("✅ $msg");
    }

    public static function noticeOk(string $msg = "")
    {
        self::println("✅ $msg");
    }

    public static function noticeErr(string $msg = "")
    {
        self::println("❌ $msg");
    }

    public static function error(string $msg = "")
    {
        self::println("❌ $msg");
    }

    public static function warn(string $msg = "")
    {
        self::println("⚠️ $msg");
    }

    public static function info(string $msg = "")
    {
        self::println($msg);
    }

    public static function notice(string $msg = "")
    {
        self::println($msg);
    }

    public static function noticeItem(string $msg = "")
    {
        self::println("🔹 $msg");
    }

    public static function noticeGroup(string $msg = "")
    {
        self::println("📦 $msg");
    }

    public static function noticeFile(string $msg = "")
    {
        self::println("📄 $msg");
    }

    public static function println(string $msg = "")
    {
        \fwrite(STDOUT, self::getPadding().$msg."\n");
    }

    public static function getPadding(): string
    {
        return \str_repeat("  ", TestRunner::getInstance()->getLevel());
    }
}
