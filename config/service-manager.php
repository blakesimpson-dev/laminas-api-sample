<?php

declare(strict_types=1);

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Domains\ItemFilter\ItemFilterProvider;
use LaminasApiSample\Domains\Profile\ProfileProvider;
use LaminasApiSample\Infrastructure\DoctrineEntityManagerFactory as DoctrineEMF;
use LaminasApiSample\Infrastructure\SystemClock;
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
        SystemClock::class => InvokableFactory::class,
    ],
    'aliases' => [
        ...$itemFilter['aliases'],
        ...$profile['aliases'],
        ClockInterface::class => SystemClock::class,
    ],
    'services' => [
        'config' => $doctrineConfig,
    ],
]);
