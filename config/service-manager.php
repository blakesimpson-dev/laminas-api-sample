<?php

declare(strict_types=1);

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Character\Handlers\CharacterReadHandler;
use LaminasApiSample\Character\Handlers\CharacterReadManyHandler;
use LaminasApiSample\DoctrineEntityManagerFactory as DoctrineEMF;
use LaminasApiSample\ItemFilter\Handlers\ItemFilterReadHandler;
use LaminasApiSample\ItemFilter\Handlers\ItemFilterReadManyHandler;
use LaminasApiSample\Profile\ProfileReadHandler;
use LaminasApiSample\Profile\ProfileReadHandlerFactory;

/** @var array<string, mixed> $doctrineConfig */
$doctrineConfig = require __DIR__ . '/doctrine.php';

return new ServiceManager([
    'factories' => [
        CharacterReadHandler::class => InvokableFactory::class,
        CharacterReadManyHandler::class => InvokableFactory::class,
        ItemFilterReadHandler::class => InvokableFactory::class,
        ItemFilterReadManyHandler::class => InvokableFactory::class,
        ProfileReadHandler::class => ProfileReadHandlerFactory::class,
        DoctrineEMF::SERVICE_NAME => DoctrineEMF::class,
    ],
    'aliases' => [
        'character' => CharacterReadHandler::class,
        'character-list' => CharacterReadManyHandler::class,
        'item-filter' => ItemFilterReadHandler::class,
        'item-filter-list' => ItemFilterReadManyHandler::class,
        'profile' => ProfileReadHandler::class,
    ],
    'services' => [
        'config' =>
            array_merge(
                $doctrineConfig,
                // ...
                // TODO(Blake Simpson): Investigate additional config the API
                // will require
            ),
    ],
]);
