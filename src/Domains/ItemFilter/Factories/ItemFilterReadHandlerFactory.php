<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter\Factories;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Domains\ItemFilter\Handlers\ItemFilterReadHandler;
use LaminasApiSample\Domains\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\Domains\ItemFilter\ItemFilterRepository;
use Override;
use Psr\Container\ContainerInterface;

final class ItemFilterReadHandlerFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): ItemFilterReadHandler {
        /** @var ItemFilterRepository $repository */
        $repository = $container->get(ItemFilterRepository::class);
        /** @var ItemFilterAdapter $adapter */
        $adapter = $container->get(ItemFilterAdapter::class);

        return new ItemFilterReadHandler($repository, $adapter);
    }
}
