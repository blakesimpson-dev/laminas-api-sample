<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Support;

use LaminasApiSample\Domains\Profile\ProfileEntity;

final class TestProfile
{
    public static function new(): ProfileEntity
    {
        return new ProfileEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'TestProfile',
        );
    }
}
