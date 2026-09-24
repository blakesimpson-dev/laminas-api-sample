<?php

declare(strict_types=1);

use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\DoctrineEntityManagerFactory as DoctrineEMF;
use LaminasApiSample\Domains\ItemFilter\ItemFilterProvider;
use LaminasApiSample\Domains\Profile\ProfileProvider;

/** @var array<string, mixed> $doctrineConfig */
$doctrineConfig = require __DIR__ . '/doctrine.php';

$itemFilter = (new ItemFilterProvider())();
$profile = (new ProfileProvider())();

return new ServiceManager([
    'factories' => [
        ...$itemFilter['factories'],
        ...$profile['factories'],
        DoctrineEMF::SERVICE_NAME => DoctrineEMF::class,
    ],
    'aliases' => [
        ...$itemFilter['aliases'],
        ...$profile['aliases'],
    ],
    'services' => [
        'config' => $doctrineConfig,
    ],
]);
