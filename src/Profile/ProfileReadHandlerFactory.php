<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\DoctrineEntityManagerFactory as DoctrineEMF;
use Override;
use Psr\Container\ContainerInterface;

final class ProfileReadHandlerFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): ProfileReadHandler {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(DoctrineEMF::SERVICE_NAME);
        /** @var ProfileRepository $repository */
        $repository = $entityManager->getRepository(ProfileEntity::class);
        $adapter = new ProfileAdapter();

        return new ProfileReadHandler($repository, $adapter);
    }
}
