<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LaminasApiSample\ItemFilter\ItemFilterEntity;
use Override;

final class ItemFilterFixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $manager->persist(new ItemFilterEntity(
            name: 'FilterBlade_1_Regular.filter',
            realm: 'pc',
            filter: '# VERSION:  8.20\n# TYPE:     1-REGULAR\n...',
            description: 'FilterBlade Regular',
            version: '3.29.3b',
        ));

        $manager->persist(new ItemFilterEntity(
            name: 'FilterBlade_2_SemiStrict.filter',
            realm: 'pc',
            filter: '# VERSION:  8.20\n# TYPE:     2-SEMI-STRICT\n...',
            description: 'FilterBlade Regular',
            version: '3.29.3b',
        ));

        $manager->persist(new ItemFilterEntity(
            name: 'FilterBlade_3_Strict.filter',
            realm: 'pc',
            filter: '# VERSION:  8.20\n# TYPE:     3-STRICT\n...',
            description: 'FilterBlade Regular',
            version: '3.29.3b',
            public: true,
        ));

        $manager->persist(new ItemFilterEntity(
            name: 'Kataplexia_OohDisMyShip_RSSF.ruthlessfilter',
            realm: 'pc',
            filter: 'Tink for Alchemy Orb\nTink for Orb of Alteration\n...',
            version: '3.29.3b',
            type: 'Ruthless',
            public: true,
        ));

        $manager->flush();
    }
}
