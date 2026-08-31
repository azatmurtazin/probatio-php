<?php

declare(strict_types=1);

namespace Probatio;

interface Runnable
{
    public function run(TestCase $tc);
}
