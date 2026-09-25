<?php

declare(strict_types=1);

use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Domains\ItemFilter\ItemFilterProvider;
use LaminasApiSample\Domains\Profile\ProfileProvider;
use LaminasApiSample\Platform\DoctrineEntityManagerFactory as DoctrineEMF;
use LaminasApiSample\Platform\SystemClock;
use Psr\Clock\ClockInterface;

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
    'invokables' => [
        ClockInterface::class => SystemClock::class,
    ],
]);
