<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile\Factories;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Domains\Profile\Handlers\ProfileReadHandler;
use LaminasApiSample\Domains\Profile\ProfileAdapter;
use LaminasApiSample\Domains\Profile\ProfileRepository;
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
        /** @var ProfileRepository $repository */
        $repository = $container->get(ProfileRepository::class);
        /** @var ProfileAdapter $adapter */
        $adapter = $container->get(ProfileAdapter::class);

        return new ProfileReadHandler($repository, $adapter);
    }
}
