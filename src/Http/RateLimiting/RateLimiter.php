<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\RateLimiting;

final readonly class RateLimiter
{
    public function __construct(
        private CounterStoreInterface $store,
        private string $policy,
        private RateLimitRule $rule,
    ) {}

    public function check(string $clientKey): RateLimitResult
    {
        $key = "ratelimit:{$this->policy}:{$this->rule->name}:{$clientKey}";

        $restrictedFor = $this->store->restrictedFor($key);
        if ($restrictedFor > 0) {
            return $this->result($this->store->current($key), $restrictedFor);
        }

        $hits = $this->store->hit($key, $this->rule->periodSeconds);
        if ($hits > $this->rule->maxHits) {
            $this->store->restrict($key, $this->rule->restrictSeconds);

            return $this->result($hits, $this->rule->restrictSeconds);
        }

        return $this->result($hits, 0);
    }

    private function result(int $hits, int $restrictedFor): RateLimitResult
    {
        return new RateLimitResult(
            $this->policy,
            $this->rule,
            $hits,
            $restrictedFor,
        );
    }
}
