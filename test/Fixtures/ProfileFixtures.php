<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Fixtures;

use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use LaminasApiSample\Domains\Profile\Embedded\TwitchEmbeddable;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use Override;

final class ProfileFixtures extends AbstractFixture
{
    public const string FIXTURE_ONE_KEY = 'profile.profile_one';
    public const string FIXTURE_TWO_KEY = 'profile.profile_two';

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $profileOne = new ProfileEntity(
            createdAt: new DateTimeImmutable('2026-01-01T00:00:00Z'),
            name: 'ProfileOne',
            locale: 'en_AU',
            twitch: new TwitchEmbeddable(name: 'TwitchOne'),
        );
        $manager->persist($profileOne);
        $this->addReference(self::FIXTURE_ONE_KEY, $profileOne);

        $profileTwo = new ProfileEntity(
            createdAt: new DateTimeImmutable('2026-01-01T12:00:00Z'),
            name: 'ProfileTwo',
            locale: 'en_AU',
        );
        $manager->persist($profileTwo);
        $this->addReference(self::FIXTURE_TWO_KEY, $profileTwo);

        $manager->flush();
    }
}
