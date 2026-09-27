<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\RateLimiting\Factories;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Http\RateLimiting\CounterStoreInterface;
use LaminasApiSample\Http\RateLimiting\RateLimiter;
use LaminasApiSample\Http\RateLimiting\RateLimitRule;
use Override;
use Psr\Container\ContainerInterface;

final class RateLimiterFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): RateLimiter {
        /** @var CounterStoreInterface $store */
        $store = $container->get(CounterStoreInterface::class);

        return new RateLimiter(
            store: $store,
            policy: 'api',
            rule: new RateLimitRule('client', 10, 5, 10),
        );
    }
}
