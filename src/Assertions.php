<?php

declare(strict_types=1);

namespace Probatio;

trait Assertions
{
    public function assertEq($expected, $actual)
    {
        return $expected === $actual
            ? $this->ok()
            : $this->fail("{$this->vd($actual)} is not equals to {$this->vd($expected)}");
    }

    public function assertTrue($value)
    {
        return $value === true
            ? $this->ok()
            : $this->fail("{$this->vd($value)} is not true");
    }

    protected function vd($x, $max = 100): string
    {
        $s = var_export($x, true);
        if (mb_strlen($s) > $max) {
            return mb_substr($s, 0, $max) . '...';
        } else {
            return $s;
        }
    }

    protected function ok()
    {
        Printer::noticeOk("assertion is ok");
        return true;
    }

    protected function fail($msg)
    {
        throw new AssertionError($msg);
    }
}
