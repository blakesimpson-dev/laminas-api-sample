<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\ItemFilter;

use LaminasApiSample\Domains\ItemFilter\ItemFilterEntity;
use LaminasApiSample\Domains\ItemFilter\ItemFilterPatch;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use RuntimeException;

#[CoversClass(ItemFilterEntity::class)]
final class ItemFilterEntityTest extends TestCase
{
    /** @throws RuntimeException */
    #[Test]
    public function constructWithValidParamsAndDefaults(): void
    {
        $entity = new ItemFilterEntity(name: 'TestFilter.filter', realm: 'pc');
        static::assertSame('TestFilter.filter', $entity->getName());
        static::assertSame('pc', $entity->getRealm());
        static::assertSame(null, $entity->getFilter());
        static::assertSame('', $entity->getDescription());
        static::assertSame('', $entity->getVersion());
        static::assertSame('Normal', $entity->getType());
        static::assertSame(false, $entity->isPublic());
        static::assertSame(true, Uuid::isValid($entity->getId()));
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
     * @throws RuntimeException
     */
    #[Test, DataProvider('getDiscretePatches')]
    public function updateAffectsOnlyPatchedFields(
        ItemFilterPatch $patch,
        array $expected,
    ): void {
        $entity = new ItemFilterEntity(name: 'TestFilter.filter', realm: 'pc');
        $before = self::getEntitySnapshot($entity);

        $entity->update($patch);

        static::assertSame(
            array_replace($before, $expected),
            self::getEntitySnapshot($entity),
        );
    }

    /** @throws RuntimeException */
    #[Test]
    public function emptyPatchHasNoEffect(): void
    {
        $entity = new ItemFilterEntity(name: 'TestFilter.filter', realm: 'pc');
        $before = self::getEntitySnapshot($entity);

        $entity->update(new ItemFilterPatch());

        static::assertSame($before, self::getEntitySnapshot($entity));
    }

    /** @throws RuntimeException */
    #[Test]
    public function publish(): void
    {
        $entity = new ItemFilterEntity(name: 'TestFilter.filter', realm: 'pc');
        $entity->publish();

        static::assertTrue($entity->isPublic());
    }

    /** @throws RuntimeException */
    #[Test]
    public function publishWhenAlreadyPublicHasNoEffect(): void
    {
        $entity = new ItemFilterEntity(
            name: 'TestFilter.filter',
            realm: 'pc',
            public: true,
        );
        $entity->publish();

        static::assertTrue($entity->isPublic());
    }
}
