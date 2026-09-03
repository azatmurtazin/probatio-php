<?php

declare(strict_types=1);

namespace Probatio;

use Composer\InstalledVersions;

use function Probatio\Functions\probatio;

use Probatio\Utils\Env;
use Probatio\Utils\Printer;

class Cli
{
    public function run()
    {
        try {
            $version = InstalledVersions::getPrettyVersion('azatmurtazin/probatio-php');
            Printer::info("Probatio: $version");
        } catch (\OutOfBoundsException $e) {
            Printer::info('Probatio: unreleased');
        }

        Printer::info('PHP version: ' . PHP_VERSION . "\n");

        $mainFile = Env::getStr('PROBATIO_MAIN_FILE', 'tests/tests.php');

        probatio()
            ->setMainFile($mainFile)
            ->registerTestFiles()
            ->run();
    }
}
