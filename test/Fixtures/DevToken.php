<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Fixtures;

enum DevToken: string
{
    case DevTokenFull = 'dev-token-full';
    case DevTokenProfileOnly = 'dev-token-profile-only';
    case DevTokenExpired = 'dev-token-expired';
    case DevTokenRevoked = 'dev-token-revoked';
    case DevTokenOtherProfile = 'dev-token-other-profile';
}
