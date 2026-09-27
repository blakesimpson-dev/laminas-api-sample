<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Support;

use DateTimeImmutable;
use Override;
use Psr\Clock\ClockInterface;

final class MutableClock implements ClockInterface
{
    public function __construct(
        private DateTimeImmutable $now,
    ) {}

    #[Override]
    public function now(): DateTimeImmutable
    {
        return $this->now;
    }

    public function advance(int $seconds): void
    {
        $this->now = $this->now->modify("+{$seconds} seconds");
    }
}
