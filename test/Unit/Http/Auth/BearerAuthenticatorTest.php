<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Http\Auth;

use DateTimeImmutable;
use Laminas\Http\Headers;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use LaminasApiSample\Domains\Auth\AccessTokenEntity;
use LaminasApiSample\Domains\Auth\AccessTokenLookupInterface;
use LaminasApiSample\Domains\Auth\AuthScope;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use LaminasApiSample\Http\Auth\AuthenticationFailedException;
use LaminasApiSample\Http\Auth\BearerAuthenticator;
use LaminasApiSampleTest\Fixtures\DevToken;
use LaminasApiSampleTest\Support\FixedTime;
use LaminasApiSampleTest\Support\TestProfile;
use Override;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\MockObject\Exception as PHPUnitMockObjectException;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

#[
    CoversClass(BearerAuthenticator::class),
    UsesClass(AccessTokenEntity::class),
    UsesClass(ProfileEntity::class),
]
final class BearerAuthenticatorTest extends TestCase
{
    /**
     * @throws NoPreviousThrowableException
     * @throws PHPUnitException
     * @throws PHPUnitMockObjectException
     * @mago-expect lint:sensitive-parameter
     */
    private function authenticator(AccessTokenEntity $knownToken): BearerAuthenticator
    {
        $lookup = $this->createStub(AccessTokenLookupInterface::class);
        $lookup
            ->method('findByPlainToken')
            ->willReturnCallback(
                // @mago-expect lint:sensitive-parameter
                static fn(string $token): ?AccessTokenEntity => $token
                    // @mago-expect lint:no-insecure-comparison
                    === DevToken::DevTokenFull->value
                        ? $knownToken
                        : null,
            );

        $clock = new class implements ClockInterface {
            #[Override]
            public function now(): DateTimeImmutable
            {
                return FixedTime::now();
            }
        };

        return new BearerAuthenticator($lookup, $clock);
    }

    private static function token(?DateTimeImmutable $expiresAt = null): AccessTokenEntity
    {
        return new AccessTokenEntity(
            createdAt: FixedTime::inThePast(),
            profile: TestProfile::new(),
            plainToken: DevToken::DevTokenFull->value,
            scopes: [AuthScope::AccountProfile->value],
            expiresAt: $expiresAt ?? FixedTime::inTheFuture(),
        );
    }

    private static function request(?string $authorization = null): HttpRequest
    {
        $request = new HttpRequest();
        if ($authorization === null) {
            return $request;
        }

        $headers = $request->getHeaders();
        if (!$headers instanceof Headers) {
            return $request;
        }

        $headers->addHeaderLine('Authorization', $authorization);
        return $request;
    }

    /** @return iterable<string, array{?string}> */
    public static function getRejectedHeaders(): iterable
    {
        yield 'missing header' => [null];
        yield 'other scheme' => ['Basic abc'];
        yield 'no token after scheme' => ['Bearer '];
        yield 'unknown token' => ['Bearer nope'];
    }

    /**
     * @throws NoPreviousThrowableException
     * @throws PHPUnitMockObjectException
     * @throws AuthenticationFailedException
     * @throws PHPUnitException
     */
    #[Test, DataProvider('getRejectedHeaders')]
    public function rejectsBadHeader(?string $authorization): void
    {
        $this->expectException(AuthenticationFailedException::class);

        $this->authenticator(self::token())->authenticate(self::request(
            $authorization,
        ));
    }

    /**
     * @throws NoPreviousThrowableException
     * @throws PHPUnitMockObjectException
     * @throws AuthenticationFailedException
     * @throws PHPUnitException
     */
    #[Test]
    public function rejectsExpiredToken(): void
    {
        $this->expectException(AuthenticationFailedException::class);

        $this->authenticator(
            self::token(expiresAt: FixedTime::ofEvent()),
        )->authenticate(self::request('Bearer '
        . DevToken::DevTokenFull->value));
    }

    /**
     * @throws NoPreviousThrowableException
     * @throws PHPUnitMockObjectException
     * @throws AuthenticationFailedException
     * @throws PHPUnitException
     */
    #[Test]
    public function rejectsRevokedToken(): void
    {
        $token = self::token();
        $token->revoke(FixedTime::ofEvent());

        $this->expectException(AuthenticationFailedException::class);

        $this->authenticator($token)->authenticate(self::request('Bearer '
        . DevToken::DevTokenFull->value));
    }

    /**
     * @throws NoPreviousThrowableException
     * @throws PHPUnitMockObjectException
     * @throws AuthenticationFailedException
     * @throws PHPUnitException
     */
    #[Test]
    public function returnsActiveToken(): void
    {
        $token = self::token();

        static::assertSame(
            $token,
            $this->authenticator($token)->authenticate(self::request('Bearer '
            . DevToken::DevTokenFull->value)),
        );
    }
}
