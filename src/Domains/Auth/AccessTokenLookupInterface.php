<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Auth;

use SensitiveParameter;

interface AccessTokenLookupInterface
{
    public function findByPlainToken(
        #[SensitiveParameter]
        string $token,
    ): ?AccessTokenEntity;
}
