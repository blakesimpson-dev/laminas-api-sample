<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;

final class ItemFilterProvider
{
    /**
     * @return array{
     *     factories: array<class-string, class-string<FactoryInterface>>,
     *     aliases: array<string, class-string>,
     * }
     */
    public function __invoke(): array
    {
        return [
            'factories' => [
                ItemFilterAdapter::class => InvokableFactory::class,
                ItemFilterRepository::class =>
                    Factories\ItemFilterRepositoryFactory::class,
                Handlers\ItemFilterReadHandler::class =>
                    Factories\ItemFilterReadHandlerFactory::class,
                Handlers\ItemFilterReadManyHandler::class =>
                    Factories\ItemFilterReadManyHandlerFactory::class,
            ],
            'aliases' => [
                'item-filter' => Handlers\ItemFilterReadHandler::class,
                'item-filter-list' => Handlers\ItemFilterReadManyHandler::class,
            ],
        ];
    }
}
