<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;

final class ProfileProvider
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
                ProfileAdapter::class => InvokableFactory::class,
                ProfileRepository::class =>
                    Factories\ProfileRepositoryFactory::class,
                Handlers\ProfileReadHandler::class =>
                    Factories\ProfileReadHandlerFactory::class,
            ],
            'aliases' => [
                'profile.read' => Handlers\ProfileReadHandler::class,
            ],
        ];
    }
}
