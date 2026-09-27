<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\Auth;

use DateTimeImmutable;
use InvalidArgumentException;
use LaminasApiSample\Domains\Auth\AccessTokenEntity;
use LaminasApiSample\Domains\Auth\AuthScope;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use LaminasApiSampleTest\Fixtures\DevToken;
use LaminasApiSampleTest\Support\FixedTime;
use LaminasApiSampleTest\Support\TestAuthScopes;
use LaminasApiSampleTest\Support\TestProfile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(AccessTokenEntity::class), UsesClass(ProfileEntity::class)]
final class AccessTokenEntityTest extends TestCase
{
    /** @throws PHPUnitException */
    #[Test]
    public function constructWithValidParamsAndDefaults(): void
    {
        $profile = TestProfile::new();
        $plainToken = DevToken::DevTokenFull->value;
        $scopes = TestAuthScopes::profileOnly();

        $entity = new AccessTokenEntity(
            createdAt: FixedTime::inThePast(),
            profile: $profile,
            plainToken: $plainToken,
            scopes: $scopes,
            expiresAt: FixedTime::inTheFuture(),
        );

        static::assertSame($profile, $entity->getProfile());
        static::assertSame($scopes, $entity->getScopes());
        static::assertTrue(Uuid::isValid($entity->getId()));

        $hasProfile = $entity->hasScope(AuthScope::AccountProfile->value);
        static::assertTrue($hasProfile);
        $hasItemFilter = $entity->hasScope(AuthScope::AccountItemFilter->value);
        static::assertFalse($hasItemFilter);

        static::assertEquals(
            FixedTime::inThePast(),
            $entity->getCreatedAt(),
            'created timestamp should be set',
        );

        static::assertNull(
            $entity->getUpdatedAt(),
            'updated timestamp should remain null',
        );

        static::assertTrue(
            $entity->isActive(FixedTime::now()),
            'a new token should be active before it expires',
        );
    }

    /** @throws PHPUnitException */
    #[Test]
    public function hashIsNotThePlainToken(): void
    {
        $hash = AccessTokenEntity::hashToken(DevToken::DevTokenFull->value);

        static::assertNotSame(DevToken::DevTokenFull->value, $hash);
        static::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $hash);
    }

    /** @throws PHPUnitException */
    #[Test]
    public function hashIsDeterministicAndDistinct(): void
    {
        static::assertSame(
            AccessTokenEntity::hashToken(DevToken::DevTokenFull->value),
            AccessTokenEntity::hashToken(DevToken::DevTokenFull->value),
        );
        static::assertNotSame(
            AccessTokenEntity::hashToken(DevToken::DevTokenFull->value),
            AccessTokenEntity::hashToken(DevToken::DevTokenExpired->value),
        );
    }

    private static function buildStubToken(
        ?DateTimeImmutable $expiresAt = null,
        ?DateTimeImmutable $revokedAt = null,
    ): AccessTokenEntity {
        return new AccessTokenEntity(
            createdAt: FixedTime::inThePast(),
            profile: TestProfile::new(),
            plainToken: DevToken::DevTokenFull->value,
            scopes: TestAuthScopes::profileOnly(),
            expiresAt: $expiresAt ?? FixedTime::inTheFuture(),
            revokedAt: $revokedAt,
        );
    }

    /** @throws PHPUnitException */
    #[Test]
    public function isActiveBeforeExpiresAt(): void
    {
        $entity = self::buildStubToken();
        static::assertTrue($entity->isActive(FixedTime::ofEvent()));
    }

    /** @throws PHPUnitException */
    #[Test]
    public function isNotActiveAtExpiresAt(): void
    {
        $entity = self::buildStubToken(expiresAt: FixedTime::ofEvent());
        static::assertFalse($entity->isActive(FixedTime::ofEvent()));
    }

    /** @throws PHPUnitException */
    #[Test]
    public function isNotActiveAfterExpiresAt(): void
    {
        $entity = self::buildStubToken(expiresAt: FixedTime::ofEvent());
        static::assertFalse($entity->isActive(FixedTime::now()));
    }

    /** @throws PHPUnitException */
    #[Test]
    public function isNotActiveAfterRevokedAt(): void
    {
        $entity = self::buildStubToken(revokedAt: FixedTime::ofEvent());
        static::assertFalse($entity->isActive(FixedTime::now()));
    }

    /** @throws PHPUnitException */
    #[Test]
    public function canRevokeToken(): void
    {
        $entity = self::buildStubToken();
        $entity->revoke(FixedTime::ofEvent());
        static::assertFalse($entity->isActive(FixedTime::now()));
    }

    /** @throws PHPUnitException */
    #[Test]
    public function emptyTokenIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AccessTokenEntity(
            createdAt: FixedTime::inThePast(),
            profile: TestProfile::new(),
            plainToken: '',
            scopes: [],
            expiresAt: FixedTime::inTheFuture(),
        );
    }
}
