<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Support;

use DateTimeImmutable;

final class FixedTime
{
    public static function getForCreate(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-01-01T00:00:00Z');
    }

    public static function getForUpdate(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-01-01T12:00:00Z');
    }
}
