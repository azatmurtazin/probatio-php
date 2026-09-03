<?php

declare(strict_types=1);

namespace Probatio\Definitions;

interface Runnable
{
    public function run(TestCase $tc);
}
