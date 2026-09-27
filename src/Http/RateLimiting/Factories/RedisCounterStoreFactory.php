<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\RateLimiting\Factories;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Infrastructure\RedisCounterStore;
use Override;
use Predis\Client;
use Psr\Container\ContainerInterface;

final class RedisCounterStoreFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null,
    ): RedisCounterStore {
        return new RedisCounterStore(new Client([
            'host' => $_ENV['REDIS_HOST'] ?? 'localhost',
            'port' => 6379,
        ]));
    }
}
