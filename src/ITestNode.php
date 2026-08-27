<?php

declare(strict_types=1);

namespace Probatio;

interface ITestNode
{
    public function run(TestCase $tc);
}
