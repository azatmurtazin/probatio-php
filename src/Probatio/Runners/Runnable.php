<?php

declare(strict_types=1);

namespace Probatio\Runners;

use Probatio\Definitions\TestCase;

interface Runnable
{
    public function run(TestCase $tc);
}
