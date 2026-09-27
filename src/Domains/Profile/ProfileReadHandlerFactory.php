<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile;

use Laminas\ServiceManager\Factory\FactoryInterface;
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
        /** @var ProfileAdapter $adapter */
        $adapter = $container->get(ProfileAdapter::class);

        return new ProfileReadHandler($adapter);
    }
}
