<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\RateLimiting;

use Override;
use Psr\Clock\ClockInterface;

final class InMemoryCounterStore implements CounterStoreInterface
{
    /**
     * @var array<string, array{
     *      count: int,
     *      expiresAt: int
     *  }> $counters
     */
    private array $counters = [];

    /** @var array<string, int> */
    private array $restrictions = [];

    public function __construct(
        private readonly ClockInterface $clock,
    ) {}

    #[Override]
    public function hit(string $key, int $periodSeconds): int
    {
        $now = $this->clock->now()->getTimestamp();
        $counter = $this->counters[$key] ?? null;
        if ($counter === null || $counter['expiresAt'] <= $now) {
            $counter = ['count' => 0, 'expiresAt' => $now + $periodSeconds];
        }

        $counter['count']++;
        $this->counters[$key] = $counter;

        return $counter['count'];
    }

    #[Override]
    public function current(string $key): int
    {
        $counter = $this->counters[$key] ?? null;
        $now = $this->clock->now()->getTimestamp();

        return $counter === null || $counter['expiresAt'] <= $now
            ? 0
            : $counter['count'];
    }

    #[Override]
    public function restrict(string $key, int $seconds): void
    {
        $this->restrictions[$key] =
            $this->clock->now()->getTimestamp() + $seconds;
    }

    #[Override]
    public function restrictedFor(string $key): int
    {
        $until = $this->restrictions[$key] ?? 0;
        return max(0, $until - $this->clock->now()->getTimestamp());
    }
}
