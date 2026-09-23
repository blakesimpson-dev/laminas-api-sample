<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LaminasApiSample\Profile\ProfileEntity;
use LaminasApiSample\Profile\Twitch;
use Override;

final class ProfileFixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        // TODO(Blake): Hydration script
        $manager->persist(
            new ProfileEntity(
                'KATAPLEXIA',
                'en_AU',
                new Twitch('Kataplexia_AU', null),
            ),
        );
        $manager->flush();
    }
}
