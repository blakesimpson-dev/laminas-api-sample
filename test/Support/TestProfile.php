<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Support;

use LaminasApiSample\Domains\Profile\ProfileEntity;

final readonly class TestProfile
{
    public static function new(): ProfileEntity
    {
        return new ProfileEntity(
            createdAt: FixedTime::inThePast(),
            name: 'TestProfile',
        );
    }
}
