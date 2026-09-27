<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\Auth;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Domains\Auth\AccessTokenLookupInterface;
use Override;
use Psr\Clock\ClockInterface;
use Psr\Container\ContainerInterface;

final class BearerAuthenticatorFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): BearerAuthenticator {
        /** @var AccessTokenLookupInterface $tokenLookup */
        $tokenLookup = $container->get(AccessTokenLookupInterface::class);
        /** @var ClockInterface $clock */
        $clock = $container->get(ClockInterface::class);

        return new BearerAuthenticator($tokenLookup, $clock);
    }
}
