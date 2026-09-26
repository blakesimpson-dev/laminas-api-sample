<?php

declare(strict_types=1);

namespace LaminasApiSample\Infrastructure;

use DateInvalidTimeZoneException;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;
use LogicException;
use Override;
use Psr\Clock\ClockInterface;

final class SystemClock implements ClockInterface
{
    #[Override]
    public function now(): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable('now', new DateTimeZone('UTC'));
        } catch (DateInvalidTimeZoneException|DateMalformedStringException $e) {
            throw new LogicException(
                'Could not create the UTC clock',
                previous: $e,
            );
        }
    }
}
