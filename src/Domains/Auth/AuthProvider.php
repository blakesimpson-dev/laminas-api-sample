<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Auth;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasApiSample\Http\Auth\AuthenticatorInterface;
use LaminasApiSample\Http\Auth\BearerAuthenticator;
use LaminasApiSample\Http\Auth\BearerAuthenticatorFactory;

final class AuthProvider
{
    /**
     * @return array{
     *     factories: array<class-string, class-string<FactoryInterface>>,
     *     aliases: array<string, class-string>,
     *  }
     */
    public function __invoke(): array
    {
        return [
            'factories' => [
                AccessTokenRepository::class =>
                    AccessTokenRepositoryFactory::class,
                BearerAuthenticator::class => BearerAuthenticatorFactory::class,
            ],
            'aliases' => [
                AccessTokenLookupInterface::class =>
                    AccessTokenRepository::class,
                AuthenticatorInterface::class => BearerAuthenticator::class,
            ],
        ];
    }
}
