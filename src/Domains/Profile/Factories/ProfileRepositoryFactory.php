<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile\Factories;

use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use LaminasApiSample\Domains\Profile\ProfileRepository;
use LaminasApiSample\Platform\DoctrineEntityManagerFactory as DoctrineEMF;
use Override;
use Psr\Container\ContainerInterface;

final class ProfileRepositoryFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): ProfileRepository {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(DoctrineEMF::SERVICE_NAME);
        /** @var ProfileRepository */
        return $entityManager->getRepository(ProfileEntity::class);
    }
}
