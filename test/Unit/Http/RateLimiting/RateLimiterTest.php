<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Http\RateLimiting;

use LaminasApiSample\Http\RateLimiting\InMemoryCounterStore;
use LaminasApiSample\Http\RateLimiting\RateLimiter;
use LaminasApiSample\Http\RateLimiting\RateLimitResult;
use LaminasApiSample\Http\RateLimiting\RateLimitRule;
use LaminasApiSampleTest\Support\FixedTime;
use LaminasApiSampleTest\Support\MutableClock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;

#[
    CoversClass(RateLimiter::class),
    UsesClass(className: InMemoryCounterStore::class),
    UsesClass(className: RateLimitRule::class),
    UsesClass(className: RateLimitResult::class),
]
final class RateLimiterTest extends TestCase
{
    private static function limiter(MutableClock $clock): RateLimiter
    {
        return new RateLimiter(
            new InMemoryCounterStore($clock),
            'api',
            new RateLimitRule(
                'client',
                maxHits: 10,
                periodSeconds: 5,
                restrictSeconds: 10,
            ),
        );
    }

    private static function hitTimes(
        RateLimiter $limiter,
        int $times,
        string $key = 'client:a',
    ): RateLimitResult {
        for ($i = 1; $i < $times; $i++) {
            $limiter->check($key);
        }

        return $limiter->check($key);
    }

    /** @throws PHPUnitException */
    #[Test]
    public function ruleHeaderUsesDocumentedFormat(): void
    {
        $limiter = self::limiter(new MutableClock(FixedTime::now()));
        $result = self::hitTimes($limiter, 1);
        static::assertSame('10:5:10', $result->rule->header());
    }

    /** @throws PHPUnitException */
    #[Test]
    public function allowsUpToMaxHits(): void
    {
        $limiter = self::limiter(new MutableClock(FixedTime::now()));
        $result = self::hitTimes($limiter, 10);
        static::assertFalse($result->isLimited());
        static::assertSame('10:5:0', $result->stateHeader());
    }

    /** @throws PHPUnitException */
    #[Test]
    public function restrictsOnTheHitOverTheLimit(): void
    {
        $limiter = self::limiter(new MutableClock(FixedTime::now()));
        $result = self::hitTimes($limiter, 11);
        static::assertTrue($result->isLimited());
        static::assertSame('11:5:10', $result->stateHeader());
    }

    /** @throws PHPUnitException */
    #[Test]
    public function remainsLimitedAfterPeriodRollsOver(): void
    {
        $clock = new MutableClock(FixedTime::now());
        $limiter = self::limiter($clock);
        self::hitTimes($limiter, 11);

        $clock->advance(6);
        $result = $limiter->check('client:a');

        static::assertTrue($result->isLimited());
        static::assertSame('0:5:4', $result->stateHeader());
    }

    /** @throws PHPUnitException */
    #[Test]
    public function recoversAfterRestriction(): void
    {
        $clock = new MutableClock(FixedTime::now());
        $limiter = self::limiter($clock);
        self::hitTimes($limiter, 11);

        $clock->advance(10);
        $result = $limiter->check('client:a');

        static::assertFalse($result->isLimited());
        static::assertSame('1:5:0', $result->stateHeader());
    }

    /** @throws PHPUnitException */
    #[Test]
    public function keysAreIndependent(): void
    {
        $limiter = self::limiter(new MutableClock(FixedTime::now()));

        $result = self::hitTimes($limiter, 11);
        $otherResult = $limiter->check('client:b');

        static::assertTrue($result->isLimited());
        static::assertSame('11:5:10', $result->stateHeader());
        static::assertFalse($otherResult->isLimited());
        static::assertSame('1:5:0', $otherResult->stateHeader());
    }
}
