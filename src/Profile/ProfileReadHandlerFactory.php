<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\DoctrineEntityManagerFactory as DoctrineEntityManagerFactory;
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
        $entityManager = $container->get(
            DoctrineEntityManagerFactory::SERVICE_NAME,
        );

        /** @var ProfileRepository $profileRepository */
        $profileRepository = $entityManager->getRepository(
            ProfileEntity::class,
        );

        return new ProfileReadHandler($profileRepository);
    }
}
