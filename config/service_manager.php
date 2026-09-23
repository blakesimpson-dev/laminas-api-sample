<?php

declare(strict_types=1);

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Character\Handlers\CharacterReadHandler;
use LaminasApiSample\Character\Handlers\CharacterReadManyHandler;
use LaminasApiSample\ItemFilter\Handlers\ItemFilterReadHandler;
use LaminasApiSample\ItemFilter\Handlers\ItemFilterReadManyHandler;
use LaminasApiSample\Profile\ProfileReadHandler;

return new ServiceManager([
    'factories' => [
        CharacterReadHandler::class => InvokableFactory::class,
        CharacterReadManyHandler::class => InvokableFactory::class,
        ItemFilterReadHandler::class => InvokableFactory::class,
        ItemFilterReadManyHandler::class => InvokableFactory::class,
        ProfileReadHandler::class => InvokableFactory::class,
    ],
    'aliases' => [
        'character' => CharacterReadHandler::class,
        'character-list' => CharacterReadManyHandler::class,
        'item-filter' => ItemFilterReadHandler::class,
        'item-filter-list' => ItemFilterReadManyHandler::class,
        'profile' => ProfileReadHandler::class,
    ],
]);
