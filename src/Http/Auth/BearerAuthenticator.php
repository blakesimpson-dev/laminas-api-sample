<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\Auth;

use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use LaminasApiSample\Domains\Auth\AccessTokenEntity;
use LaminasApiSample\Domains\Auth\AccessTokenLookupInterface;
use Override;
use Psr\Clock\ClockInterface;

final class BearerAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private readonly AccessTokenLookupInterface $tokenLookup,
        private readonly ClockInterface $clock,
    ) {}

    /** @throws AuthenticationFailedException */
    #[Override]
    public function authenticate(HttpRequest $request): AccessTokenEntity
    {
        $header = $request->getHeader('Authorization');
        if (!$header instanceof HeaderInterface) {
            throw new AuthenticationFailedException(
                'Missing Authorization header',
            );
        }

        /** @var HeaderInterface $header */
        $plainToken = self::extractBearerToken($header->getFieldValue());
        if ($plainToken === null) {
            throw new AuthenticationFailedException(
                'Malformed Authorization header',
            );
        }

        $accessToken = $this->tokenLookup->findByPlainToken($plainToken);
        if ($accessToken === null) {
            throw new AuthenticationFailedException('Unknown token');
        }

        if (!$accessToken->isActive($this->clock->now())) {
            throw new AuthenticationFailedException('Token expired or revoked');
        }

        return $accessToken;
    }

    private static function extractBearerToken(string $value): ?string
    {
        if (!str_starts_with($value, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($value, 7));
        return $token === '' ? null : $token;
    }
}
