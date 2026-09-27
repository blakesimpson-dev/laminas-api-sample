<?php

declare(strict_types=1);

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Domains\Auth\AuthProvider;
use LaminasApiSample\Domains\ItemFilter\ItemFilterProvider;
use LaminasApiSample\Domains\Profile\ProfileProvider;
use LaminasApiSample\Http\RateLimiting\RateLimitProvider;
use LaminasApiSample\Infrastructure\DoctrineEntityManagerFactory as DoctrineEMF;
use LaminasApiSample\Infrastructure\SystemClock;
use Psr\Clock\ClockInterface;

/** @var array<string, mixed> $doctrineConfig */
$doctrineConfig = require __DIR__ . '/doctrine.php';

$authProvider = (new AuthProvider())();
$itemFilterProvider = (new ItemFilterProvider())();
$profileProvider = (new ProfileProvider())();
$rateLimitProvider = (new RateLimitProvider())();

return new ServiceManager([
    'factories' => [
        ...$authProvider['factories'],
        ...$itemFilterProvider['factories'],
        ...$profileProvider['factories'],
        ...$rateLimitProvider['factories'],
        DoctrineEMF::SERVICE_NAME => DoctrineEMF::class,
        SystemClock::class => InvokableFactory::class,
    ],
    'aliases' => [
        ...$authProvider['aliases'],
        ...$itemFilterProvider['aliases'],
        ...$profileProvider['aliases'],
        ...$rateLimitProvider['aliases'],
        ClockInterface::class => SystemClock::class,
    ],
    'services' => [
        'config' => $doctrineConfig,
    ],
]);
