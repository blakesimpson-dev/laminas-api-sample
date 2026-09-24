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
        // @mago-expect lint:no-shorthand-ternary
        foreach (glob(__DIR__ . '/Data/*.filter') ?: [] as $path) {
            $content = file_get_contents($path);
            if (!$content) {
                continue;
            }

            $matches = null;
            preg_match_all('/^#\s*([A-Z]+):\s*(.+)$/m', $content, $matches);

            $headerKeys = $matches[1] ?? null;
            $headerValues = $matches[2] ?? null;
            if (!$headerKeys || !$headerValues) {
                continue;
            }

            $header = array_combine($headerKeys, array_map(
                'trim',
                $headerValues,
            ));

            $description = implode(' - ', [
                $header['AUTHOR'] ?? 'AUTHOR',
                $header['TYPE'] ?? 'TYPE',
                $header['STYLE'] ?? 'STYLE',
            ]);

            $manager->persist(new ItemFilterEntity(
                name: basename($path),
                realm: 'pc',
                filter: $content,
                description: $description,
                version: $header['VERSION'] ?? 'VERSION',
            ));
        }

        $manager->flush();
    }
}
