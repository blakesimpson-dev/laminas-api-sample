<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\RateLimiting;

final readonly class RateLimitResult
{
    public function __construct(
        public string $policy,
        public RateLimitRule $rule,
        public int $hits,
        public int $restrictedFor,
    ) {}

    public function isLimited(): bool
    {
        return $this->restrictedFor > 0;
    }

    public function stateHeader(): string
    {
        return (
            "{$this->hits}:"
            . "{$this->rule->periodSeconds}:"
            . "{$this->restrictedFor}"
        );
    }
}
