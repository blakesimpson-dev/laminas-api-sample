<?php

declare(strict_types=1);

namespace LaminasApiSample\Infrastructure;

use LaminasApiSample\Http\RateLimiting\CounterStore;
use Override;
use Predis\Client;
use Predis\Transaction\MultiExec;

final readonly class RedisCounterStore implements CounterStore
{
    public function __construct(
        private Client $redis,
    ) {}

    #[Override]
    public function hit(string $key, int $periodSeconds): int
    {
        /** @var array{0: int|string, 1: int|string} $replies */
        $replies = $this->redis->transaction(static function (MultiExec $tx) use (
            $key,
            $periodSeconds,
        ): void {
            $tx->incr($key);
            $tx->expire($key, $periodSeconds, 'NX');
        });

        return (int) $replies[0];
    }

    #[Override]
    public function current(string $key): int
    {
        $value = $this->redis->get($key);

        return $value === null ? 0 : (int) $value;
    }

    #[Override]
    public function restrict(string $key, int $seconds): void
    {
        $this->redis->set("{$key}:restricted", '1', 'EX', $seconds);
    }

    #[Override]
    public function restrictedFor(string $key): int
    {
        // Note: TTL returns -2 for a missing key and -1 for no expiry
        return max(0, (int) $this->redis->ttl("{$key}:restricted"));
    }
}
