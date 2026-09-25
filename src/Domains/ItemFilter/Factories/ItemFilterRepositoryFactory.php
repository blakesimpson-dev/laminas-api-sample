<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter\Factories;

use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Domains\ItemFilter\ItemFilterEntity;
use LaminasApiSample\Domains\ItemFilter\ItemFilterRepository;
use LaminasApiSample\Platform\DoctrineEntityManagerFactory as DoctrineEMF;
use Override;
use Psr\Container\ContainerInterface;

final class ItemFilterRepositoryFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): ItemFilterRepository {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(DoctrineEMF::SERVICE_NAME);
        /** @var ItemFilterRepository */
        return $entityManager->getRepository(ItemFilterEntity::class);
    }
}
