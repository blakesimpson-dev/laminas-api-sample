<?php

declare(strict_types=1);

namespace LaminasApiSample\ItemFilter\Factories;

use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\DoctrineEntityManagerFactory as DoctrineEMF;
use LaminasApiSample\ItemFilter\Handlers\ItemFilterReadHandler;
use LaminasApiSample\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\ItemFilter\ItemFilterEntity;
use LaminasApiSample\ItemFilter\ItemFilterRepository;
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
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(DoctrineEMF::SERVICE_NAME);
        /** @var ItemFilterRepository $repository */
        $repository = $entityManager->getRepository(ItemFilterEntity::class);
        $adapter = new ItemFilterAdapter();

        return new ItemFilterReadHandler($repository, $adapter);
    }
}
