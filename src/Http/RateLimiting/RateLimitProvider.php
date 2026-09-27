<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\RateLimiting;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Infrastructure\RedisCounterStore;

final class RateLimitProvider
{
    /**
     * @return array{
     *     factories: array<class-string, class-string<FactoryInterface>>,
     *     aliases: array<string, class-string>,
     *  }
     */
    public function __invoke(): array
    {
        return [
            'factories' => [
                RateLimiter::class => Factories\RateLimiterFactory::class,
                RedisCounterStore::class =>
                    Factories\RedisCounterStoreFactory::class,
            ],
            'aliases' => [
                CounterStoreInterface::class => RedisCounterStore::class,
            ],
        ];
    }
}
