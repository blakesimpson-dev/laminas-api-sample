<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Support;

use DateTimeImmutable;

final readonly class FixedTime
{
    public static function inThePast(): DateTimeImmutable
    {
        return new DateTimeImmutable('2025-01-01T00:00:00Z');
    }

    public static function ofEvent(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-01-01T00:00:00Z');
    }

    public static function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-03-01T12:00:00Z');
    }

    public static function inTheFuture(): DateTimeImmutable
    {
        return new DateTimeImmutable('2099-01-01T12:00:00Z');
    }
}
