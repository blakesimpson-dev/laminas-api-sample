<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Auth;

use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Infrastructure\DoctrineEntityManagerFactory as DoctrineEMF;
use Override;
use Psr\Container\ContainerInterface;

final class AccessTokenRepositoryFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): AccessTokenRepository {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(DoctrineEMF::SERVICE_NAME);

        /** @var AccessTokenRepository */
        return $entityManager->getRepository(AccessTokenEntity::class);
    }
}
