<?php

declare(strict_types=1);

namespace LaminasApiSample\Platform;

use DateInvalidTimeZoneException;
use DateTimeImmutable;
use DateTimeZone;
use Override;
use Psr\Clock\ClockInterface;

final class SystemClock implements ClockInterface
{
    /** @throws DateInvalidTimeZoneException */
    #[Override]
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
