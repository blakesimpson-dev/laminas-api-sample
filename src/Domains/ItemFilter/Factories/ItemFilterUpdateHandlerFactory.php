<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter\Factories;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Domains\ItemFilter\Handlers\ItemFilterUpdateHandler;
use LaminasApiSample\Domains\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\Domains\ItemFilter\ItemFilterRepository;
use LaminasApiSample\Domains\ItemFilter\Validation\ItemFilterUpdateValidator;
use Override;
use Psr\Container\ContainerInterface;

final class ItemFilterUpdateHandlerFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): ItemFilterUpdateHandler {
        /** @var ItemFilterRepository $repository */
        $repository = $container->get(ItemFilterRepository::class);
        /** @var ItemFilterAdapter $adapter */
        $adapter = $container->get(ItemFilterAdapter::class);
        $validator = new ItemFilterUpdateValidator();

        return new ItemFilterUpdateHandler($repository, $adapter, $validator);
    }
}
