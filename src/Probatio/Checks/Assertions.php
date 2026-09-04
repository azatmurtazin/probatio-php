<?php

declare(strict_types=1);

namespace Probatio\Checks;

use function Probatio\Functions\probatio;

use Probatio\Utils\Location;
use Probatio\Utils\Printer;

trait Assertions
{
    public function assertSame($expected, $actual, string $tpl = '%s is not identical to %s')
    {
        $this->process($expected === $actual, $tpl, [$actual, $expected]);
    }

    public function assertNotSame($expected, $actual, string $tpl = '%s is identical to %s')
    {
        $this->process($expected !== $actual, $tpl, [$actual, $expected]);
    }

    public function assertEquals($expected, $actual, string $tpl = '%s is not equals to %s')
    {
        $this->process($expected == $actual, $tpl, [$actual, $expected]);
    }

    public function assertTrue($value, string $tpl = '%s is not true')
    {
        $this->process($value === true, $tpl, [$value]);
    }

    public function assertNotTrue($value, string $tpl = '%s is true')
    {
        $this->process($value !== true, $tpl, [$value]);
    }

    public function assertBetween($actual, $min, $max, string $tpl = '%s is not between %s:%s')
    {
        $res = ($min <= $actual) && ($actual <= $max);
        $this->process($res, $tpl, [$actual, $min, $max]);
    }

    public function assertNotBetween($actual, $min, $max, string $tpl = '%s is between %s:%s')
    {
        $res = ($min <= $actual) && ($actual <= $max);
        $this->process(!$res, $tpl, [$actual, $min, $max]);
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

    /**
     * process()
     * @param bool $cond
     * @param string $tpl
     * @param mixed[] $values
     * @return void
     */
    protected function process(bool $cond, string $tpl, array $values)
    {
        $stats = probatio()->stats();
        if ($cond) {
            [$file, $line] = Location::fromCaller()->toArray();
            Printer::noticeOk("assertion is ok ($file:$line)");
            $stats->incOkAsserts();
        } else {
            $stats->incErrAsserts();
            $strValues = array_map(function ($v) {
                return $this->vd($v);
            }, $values);
            $msg = \sprintf($tpl, ...$strValues);
            throw new AssertionError($msg);
        }
    }
}
