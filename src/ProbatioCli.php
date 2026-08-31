<?php

declare(strict_types=1);

namespace Probatio;

class ProbatioCli
{
    public function run()
    {
        try {
            $version = \Composer\InstalledVersions::getPrettyVersion('azatmurtazin/probatio-php');
            Printer::info("Probatio: $version");
        } catch (\OutOfBoundsException $e) {
            Printer::info("Probatio: unreleased");
        }

        $mainFile = Env::getStr("PROBATIO_MAIN_FILE", "tests/tests.php");

        TestSuite::getInstance()
            ->setMainFile($mainFile)
            ->registerTestFiles()
            ->run();
    }
}
