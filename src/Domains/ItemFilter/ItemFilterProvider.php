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
     *  }
     */
    public function __invoke(): array
    {
        return [
            'factories' => [
                ItemFilterAdapter::class => InvokableFactory::class,
                Validation\ItemFilterCreateValidator::class =>
                    InvokableFactory::class,
                Validation\ItemFilterUpdateValidator::class =>
                    InvokableFactory::class,
                ItemFilterRepository::class =>
                    Factories\ItemFilterRepositoryFactory::class,
                Handlers\ItemFilterReadHandler::class =>
                    Factories\ItemFilterReadHandlerFactory::class,
                Handlers\ItemFilterReadManyHandler::class =>
                    Factories\ItemFilterReadManyHandlerFactory::class,
                Handlers\ItemFilterCreateHandler::class =>
                    Factories\ItemFilterCreateHandlerFactory::class,
                Handlers\ItemFilterUpdateHandler::class =>
                    Factories\ItemFilterUpdateHandlerFactory::class,
            ],
            'aliases' => [
                'item-filter.read' => Handlers\ItemFilterReadHandler::class,
                'item-filter.read-many' =>
                    Handlers\ItemFilterReadManyHandler::class,
                'item-filter.create' => Handlers\ItemFilterCreateHandler::class,
                'item-filter.update' => Handlers\ItemFilterUpdateHandler::class,
            ],
        ];
    }
}
