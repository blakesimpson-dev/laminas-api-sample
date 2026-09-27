<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Fixtures;

use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LaminasApiSample\Domains\Auth\AccessTokenEntity;
use LaminasApiSample\Domains\Auth\Scope;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use Override;

final class AccessTokenFixtures extends AbstractFixture implements
    DependentFixtureInterface
{
    #[Override]
    public function getDependencies(): array
    {
        return [ProfileFixtures::class];
    }

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $profileOne = $this->getReference(
            ProfileFixtures::FIXTURE_ONE_KEY,
            ProfileEntity::class,
        );
        $profileTwo = $this->getReference(
            ProfileFixtures::FIXTURE_TWO_KEY,
            ProfileEntity::class,
        );
        $createdAt = new DateTimeImmutable('2026-01-02T00:00:00Z');

        $devTokenFull = new AccessTokenEntity(
            createdAt: $createdAt,
            profile: $profileOne,
            // @mago-expect lint:no-literal-password
            plainToken: 'dev-token-full',
            scopes: [
                Scope::AccountProfile->value,
                Scope::AccountItemFilter->value,
            ],
            expiresAt: new DateTimeImmutable('2099-01-01T00:00:00Z'),
        );
        $manager->persist($devTokenFull);

        $devTokenProfileOnly = new AccessTokenEntity(
            createdAt: $createdAt,
            profile: $profileOne,
            // @mago-expect lint:no-literal-password
            plainToken: 'dev-token-profile-only',
            scopes: [Scope::AccountProfile->value],
            expiresAt: new DateTimeImmutable('2099-01-01T00:00:00Z'),
        );
        $manager->persist($devTokenProfileOnly);

        $devTokenExpired = new AccessTokenEntity(
            createdAt: $createdAt,
            profile: $profileOne,
            // @mago-expect lint:no-literal-password
            plainToken: 'dev-token-expired',
            scopes: [
                Scope::AccountProfile->value,
                Scope::AccountItemFilter->value,
            ],
            expiresAt: new DateTimeImmutable('2026-02-01T00:00:00Z'),
        );
        $manager->persist($devTokenExpired);

        $devTokenRevoked = new AccessTokenEntity(
            createdAt: $createdAt,
            profile: $profileOne,
            // @mago-expect lint:no-literal-password
            plainToken: 'dev-token-revoked',
            scopes: [
                Scope::AccountProfile->value,
                Scope::AccountItemFilter->value,
            ],
            expiresAt: new DateTimeImmutable('2099-01-01T00:00:00Z'),
            revokedAt: new DateTimeImmutable('2026-03-01T00:00:00Z'),
        );
        $manager->persist($devTokenRevoked);

        $devTokenOther = new AccessTokenEntity(
            createdAt: $createdAt,
            profile: $profileTwo,
            // @mago-expect lint:no-literal-password
            plainToken: 'dev-token-other-profile',
            scopes: [
                Scope::AccountProfile->value,
                Scope::AccountItemFilter->value,
            ],
            expiresAt: new DateTimeImmutable('2099-01-01T00:00:00Z'),
        );
        $manager->persist($devTokenOther);

        $manager->flush();
    }
}
