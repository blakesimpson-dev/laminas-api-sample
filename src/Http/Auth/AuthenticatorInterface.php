<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\Auth;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use LaminasApiSample\Domains\Auth\AccessTokenEntity;

interface AuthenticatorInterface
{
    /** @throws AuthenticationFailedException */
    public function authenticate(HttpRequest $request): AccessTokenEntity;
}
