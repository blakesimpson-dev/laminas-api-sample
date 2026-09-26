<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\ItemFilter;

use LaminasApiSample\Domains\ItemFilter\ItemFilterEntity;
use LaminasApiSample\Domains\ItemFilter\ItemFilterPatch;
use LaminasApiSampleTest\Support\FixedTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(ItemFilterEntity::class)]
final class ItemFilterEntityTest extends TestCase
{
    /** @throws PHPUnitException */
    #[Test]
    public function constructWithValidParamsAndDefaults(): void
    {
        $entity = new ItemFilterEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'TestFilter.filter',
            realm: 'pc',
        );

        static::assertSame('TestFilter.filter', $entity->getName());
        static::assertSame('pc', $entity->getRealm());
        static::assertNull($entity->getFilter());
        static::assertSame('', $entity->getDescription());
        static::assertSame('', $entity->getVersion());
        static::assertSame('Normal', $entity->getType());
        static::assertFalse($entity->isPublic());
        static::assertTrue(Uuid::isValid($entity->getId()));

        static::assertEquals(
            FixedTime::getForCreate(),
            $entity->getCreatedAt(),
            'created timestap should be set',
        );
        static::assertSame(
            null,
            $entity->getUpdatedAt(),
            'updated timestamp should remain null',
        );
    }

    /** @return array<string, mixed> */
    private static function getEntitySnapshot(ItemFilterEntity $entity): array
    {
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'realm' => $entity->getRealm(),
            'filter' => $entity->getFilter(),
            'description' => $entity->getDescription(),
            'version' => $entity->getVersion(),
            'type' => $entity->getType(),
            'public' => $entity->isPublic(),
            'createdAt' => $entity->getCreatedAt()->format(DATE_ATOM),
            'updatedAt' => $entity->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }

    /** @return iterable<string, array{ItemFilterPatch, array<string, string>}> */
    public static function getDiscretePatches(): iterable
    {
        yield 'update name' => [
            new ItemFilterPatch(name: 'UpdatedName.filter'),
            ['name' => 'UpdatedName.filter'],
        ];

        yield 'update realm' => [
            new ItemFilterPatch(realm: 'xbox'),
            ['realm' => 'xbox'],
        ];

        yield 'update filter' => [
            new ItemFilterPatch(filter: 'Updated filter content'),
            ['filter' => 'Updated filter content'],
        ];

        yield 'update description' => [
            new ItemFilterPatch(description: 'Updated description content'),
            ['description' => 'Updated description content'],
        ];

        yield 'update version' => [
            new ItemFilterPatch(version: 'UpdatedVersion'),
            ['version' => 'UpdatedVersion'],
        ];

        yield 'update type' => [
            new ItemFilterPatch(type: 'Ruthless'),
            ['type' => 'Ruthless'],
        ];
    }

    /**
     * @param array<string, string> $expected
     * @throws PHPUnitException
     */
    #[Test, DataProvider('getDiscretePatches')]
    public function updateAffectsOnlyPatchedFields(
        ItemFilterPatch $patch,
        array $expected,
    ): void {
        $entity = new ItemFilterEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'TestFilter.filter',
            realm: 'pc',
        );
        $before = self::getEntitySnapshot($entity);
        $entity->update($patch, FixedTime::getForUpdate());

        static::assertSame(array_replace($before, $expected, [
            'updatedAt' => FixedTime::getForUpdate()->format(DATE_ATOM),
        ]), self::getEntitySnapshot($entity));
    }

    /** @throws PHPUnitException */
    #[Test]
    public function emptyPatchHasNoEffect(): void
    {
        $entity = new ItemFilterEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'TestFilter.filter',
            realm: 'pc',
        );
        $before = self::getEntitySnapshot($entity);
        $entity->update(new ItemFilterPatch(), FixedTime::getForUpdate());

        static::assertSame($before, self::getEntitySnapshot($entity));
        static::assertSame(
            $before['updatedAt'] ?? null,
            $entity->getUpdatedAt()?->format(DATE_ATOM),
            'updated timestamp should not change',
        );
    }

    /** @throws PHPUnitException */
    #[Test]
    public function publish(): void
    {
        $entity = new ItemFilterEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'TestFilter.filter',
            realm: 'pc',
        );
        $entity->publish(FixedTime::getForUpdate());

        static::assertTrue($entity->isPublic());
        static::assertEquals(
            FixedTime::getForUpdate(),
            $entity->getUpdatedAt(),
            'updated timestamp should change',
        );
    }

    /** @throws PHPUnitException */
    #[Test]
    public function publishWhenAlreadyPublicHasNoEffect(): void
    {
        $entity = new ItemFilterEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'TestFilter.filter',
            realm: 'pc',
            public: true,
        );
        $before = self::getEntitySnapshot($entity);
        $entity->publish(FixedTime::getForUpdate());

        static::assertTrue($entity->isPublic());
        static::assertSame(
            $before['updatedAt'] ?? null,
            $entity->getUpdatedAt()?->format(DATE_ATOM),
            'updated timestamp should not change',
        );
    }
}
