<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Support;

use LaminasApiSample\Domains\Auth\AuthScope;

final readonly class TestAuthScopes
{
    /** @return list<string> */
    public static function all(): array
    {
        return [
            AuthScope::AccountItemFilter->value,
            AuthScope::AccountProfile->value,
        ];
    }

    /** @return list<string> */
    public static function profileOnly(): array
    {
        return [
            AuthScope::AccountProfile->value,
        ];
    }

    /** @return list<string> */
    public static function itemFilterOnly(): array
    {
        return [
            AuthScope::AccountItemFilter->value,
        ];
    }
}
