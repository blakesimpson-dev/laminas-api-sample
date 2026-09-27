<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Fixtures;

use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LaminasApiSample\Domains\Auth\AccessTokenEntity;
use LaminasApiSample\Domains\Auth\AuthScope;
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

        $fullScope = [
            AuthScope::AccountProfile->value,
            AuthScope::AccountItemFilter->value,
        ];
        $limitedScope = [
            AuthScope::AccountProfile->value,
        ];

        $fixedCreatedAt = new DateTimeImmutable('2026-01-02T00:00:00Z');
        $fixedExpiresAt = new DateTimeImmutable('2099-01-01T00:00:00Z');

        $devTokenFull = new AccessTokenEntity(
            createdAt: $fixedCreatedAt,
            profile: $profileOne,
            plainToken: DevToken::DevTokenFull->value,
            scopes: $fullScope,
            expiresAt: $fixedExpiresAt,
        );
        $manager->persist($devTokenFull);

        $devTokenProfileOnly = new AccessTokenEntity(
            createdAt: $fixedCreatedAt,
            profile: $profileOne,
            plainToken: DevToken::DevTokenProfileOnly->value,
            scopes: $limitedScope,
            expiresAt: $fixedExpiresAt,
        );
        $manager->persist($devTokenProfileOnly);

        $devTokenExpired = new AccessTokenEntity(
            createdAt: $fixedCreatedAt,
            profile: $profileOne,
            plainToken: DevToken::DevTokenExpired->value,
            scopes: $fullScope,
            expiresAt: new DateTimeImmutable('2026-02-01T00:00:00Z'),
        );
        $manager->persist($devTokenExpired);

        $devTokenRevoked = new AccessTokenEntity(
            createdAt: $fixedCreatedAt,
            profile: $profileOne,
            plainToken: DevToken::DevTokenRevoked->value,
            scopes: $fullScope,
            expiresAt: $fixedExpiresAt,
            revokedAt: new DateTimeImmutable('2026-03-01T00:00:00Z'),
        );
        $manager->persist($devTokenRevoked);

        $devTokenOther = new AccessTokenEntity(
            createdAt: $fixedCreatedAt,
            profile: $profileTwo,
            plainToken: DevToken::DevTokenOtherProfile->value,
            scopes: $fullScope,
            expiresAt: $fixedExpiresAt,
        );
        $manager->persist($devTokenOther);

        $manager->flush();
    }
}
