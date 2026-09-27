<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\RateLimiting;

interface CounterStore
{
    public function hit(string $key, int $periodSeconds): int;

    public function current(string $key): int;

    public function restrict(string $key, int $seconds): void;

    public function restrictedFor(string $key): int;
}
