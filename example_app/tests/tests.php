<?php

declare(strict_types=1);

require_once __DIR__."/../../vendor/autoload.php";

Epreuve\TestSuite::getInstance()
    ->setMainFile(__FILE__)
    ->maybeRunAllTests();
