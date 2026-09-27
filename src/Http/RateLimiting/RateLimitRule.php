<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\RateLimiting;

final readonly class RateLimitRule
{
    public function __construct(
        public string $name,
        public int $maxHits,
        public int $periodSeconds,
        public int $restrictSeconds,
    ) {}

    public function header(): string
    {
        return (
            "{$this->maxHits}:"
            . "{$this->periodSeconds}:"
            . "{$this->restrictSeconds}"
        );
    }
}
