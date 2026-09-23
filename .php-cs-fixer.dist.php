<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()->in([
    __DIR__ . "/src",
    __DIR__ . "/public",
]);
$config = new Config();

return $config
    ->setRules([
        "@PER-CS2x0" => true,
        "declare_strict_types" => true,
        "no_unused_imports" => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
