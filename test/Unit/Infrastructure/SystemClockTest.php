<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Infrastructure;

use DateTimeImmutable;
use LaminasApiSample\Infrastructure\SystemClock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;

#[CoversClass(SystemClock::class)]
final class SystemClockTest extends TestCase
{
    /** @throws PHPUnitException */
    #[Test]
    public function nowIsInUtc(): void
    {
        static::assertSame(
            'UTC',
            new SystemClock()->now()->getTimezone()->getName(),
        );
    }

    /** @throws PHPUnitException */
    #[Test]
    public function nowIsTheCurrentTime(): void
    {
        $before = new DateTimeImmutable();
        $now = new SystemClock()->now();
        $after = new DateTimeImmutable();

        static::assertGreaterThanOrEqual($before, $now);
        static::assertLessThanOrEqual($after, $now);
    }
}
