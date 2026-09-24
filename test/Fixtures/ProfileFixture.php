<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LaminasApiSample\Domains\Profile\Embedded\TwitchEmbeddable;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use Override;

final class ProfileFixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            new ProfileEntity(
                'KATAPLEXIA',
                'en_AU',
                new TwitchEmbeddable('Kataplexia_AU', null),
            ),
        );
        $manager->flush();
    }
}
