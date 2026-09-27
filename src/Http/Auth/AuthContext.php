<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\Auth;

use LaminasApiSample\Domains\Profile\ProfileEntity;

final readonly class AuthContext
{
    /** @param list<string> $scopes */
    public function __construct(
        public ProfileEntity $profile,
        public array $scopes,
    ) {}
}
