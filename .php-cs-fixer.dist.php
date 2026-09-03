<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@auto' => true,
        '@PSR12' => true,
        'trailing_comma_in_multiline' => [
            'elements' => [],
        ],
        'simple_to_complex_string_variable' => true,
        'single_quote' => true,
        'ordered_imports' => true,
    ])
    ->setFinder(
        (new Finder())
            ->in(__DIR__)
    )
;
