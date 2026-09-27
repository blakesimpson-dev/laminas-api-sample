<?php

declare(strict_types=1);

namespace LaminasApiSample\Infrastructure;

use ArgumentCountError;
use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Override;
use Psr\Container\ContainerInterface;
use Roave\PsrContainerDoctrine\EntityManagerFactory as RoaveEMF;
use RuntimeException;
use ValueError;

final class DoctrineEntityManagerFactory implements FactoryInterface
{
    public const string SERVICE_NAME = 'doctrine.entity_manager.orm_default';

    /**
     * @throws RuntimeException
     * @throws ValueError
     * @throws ArgumentCountError
     */
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): EntityManager {
        $entityManager = (new RoaveEMF())($container);

        if (!$entityManager instanceof EntityManager) {
            throw new RuntimeException(sprintf(
                'Expected %s, got %s.',
                EntityManager::class,
                get_debug_type($entityManager),
            ));
        }

        return $entityManager;
    }
}
