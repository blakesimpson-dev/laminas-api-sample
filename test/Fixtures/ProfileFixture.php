<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Fixtures;

use DateTimeImmutable;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LaminasApiSample\Domains\Profile\Embedded\StreamEmbeddable;
use LaminasApiSample\Domains\Profile\Embedded\TwitchEmbeddable;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use Override;

final class ProfileFixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $manager->persist(new ProfileEntity(
            createdAt: new DateTimeImmutable('2026-01-01T00:00:00Z'),
            name: 'KATAPLEXIA',
            locale: 'en_AU',
            twitch: new TwitchEmbeddable(
                name: 'Kataplexia_AU',
                stream: new StreamEmbeddable(
                    name: 'Chill stream!',
                    image: 'https://i.imgur.com/eb6OXqb.jpeg',
                    status: 'Online',
                ),
            ),
        ));
        $manager->flush();
    }
}
