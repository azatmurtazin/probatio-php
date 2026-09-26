<?php

declare(strict_types=1);

namespace Probatio;

use Composer\InstalledVersions;

use function Probatio\Functions\probatio;

use Probatio\Utils\Printer;
use Probatio\Utils\PrinterBackend;

class Cli
{
    public function run()
    {
        $php_version = PHP_VERSION;
        $version = 'unknown';
        try {
            $version = InstalledVersions::getPrettyVersion('azatmurtazin/probatio-php');
        } catch (\OutOfBoundsException $e) {
            $version = 'unknown';
        }

        $config = probatio()->config();

        Printer::addBackend(new PrinterBackend(STDOUT, $config->verbosity()));
        Printer::addBackend(new PrinterBackend('log/tests.log', $config->verbosity(), true));

        Printer::info("Probatio: $version; PHP version: $php_version");
        Printer::breakLine();

        probatio()
            ->registerTestFiles()
            ->run();
    }
}
