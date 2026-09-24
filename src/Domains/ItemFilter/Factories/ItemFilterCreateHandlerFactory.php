<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter\Factories;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Domains\ItemFilter\Handlers\ItemFilterCreateHandler;
use LaminasApiSample\Domains\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\Domains\ItemFilter\ItemFilterRepository;
use LaminasApiSample\Domains\ItemFilter\Validation\ItemFilterCreateValidator;
use Override;
use Psr\Container\ContainerInterface;

final class ItemFilterCreateHandlerFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): ItemFilterCreateHandler {
        /** @var ItemFilterRepository $repository */
        $repository = $container->get(ItemFilterRepository::class);
        /** @var ItemFilterAdapter $adapter */
        $adapter = $container->get(ItemFilterAdapter::class);
        $validator = new ItemFilterCreateValidator();

        return new ItemFilterCreateHandler($repository, $adapter, $validator);
    }
}
