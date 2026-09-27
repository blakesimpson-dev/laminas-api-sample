<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Support;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use LaminasApiSample\Domains\Auth\AccessTokenEntity;
use LaminasApiSample\Http\Auth\AuthenticationFailedException;
use LaminasApiSample\Http\Auth\AuthenticatorInterface;
use Override;

final class StubAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        // @mago-expect lint:sensitive-parameter
        private readonly ?AccessTokenEntity $token,
    ) {}

    #[Override]
    public function authenticate(HttpRequest $request): AccessTokenEntity
    {
        if ($this->token === null) {
            throw new AuthenticationFailedException('Mock rejection');
        }

        return $this->token;
    }
}
