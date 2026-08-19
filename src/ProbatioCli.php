<?php

declare(strict_types=1);

namespace Probatio;

class ProbatioCli
{
    public function run()
    {
        $cwd = getcwd();
        $mainFile = "$cwd/tests/tests.php";
        TestSuite::getInstance()
            ->setMainFile($mainFile)
            ->registerTests()
            ->run();
    }
}
