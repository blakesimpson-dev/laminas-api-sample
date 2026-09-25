<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\ItemFilter;

use DomainException;
use LaminasApiSample\Domains\ItemFilter\ItemFilterEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use RuntimeException;

#[CoversClass(className: ItemFilterEntity::class)]
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
            'filter_name' => $entity->getName(),
            'realm' => $entity->getRealm(),
            'filter' => $entity->getFilter(),
            'description' => $entity->getDescription(),
            'version' => $entity->getVersion(),
            'type' => $entity->getType(),
            'public' => $entity->isPublic(),
        ];
    }

    /**
     * @return iterable<string, array{
     *      array{
     *          filter_name?: string,
     *          realm?: string,
     *          filter?: string,
     *          description?: string,
     *          version?: string,
     *          type?: string,
     *          public?: bool
     *      }
     * }>
     */
    public static function getDiscreteChangeset(): iterable
    {
        yield 'filter_name' => [['filter_name' => 'UpdatedName.filter']];
        yield 'realm' => [['realm' => 'xbox']];
        yield 'filter' => [['filter' => 'Updated filter content']];
        yield 'description' => [[
            'description' => 'Updated description content',
        ]];
        yield 'version' => [['version' => 'UpdatedVersion']];
        yield 'type' => [['type' => 'Ruthless']];
        yield 'public' => [['public' => true]];
    }

    /**
     * @param array{
     *      filter_name?: string,
     *      realm?: string,
     *      filter?: string,
     *      description?: string,
     *      version?: string,
     *      type?: string,
     *      public?: bool
     * } $changeset
     * @throws RuntimeException
     */
    #[Test, DataProvider('getDiscreteChangeset')]
    public function updateAffectsOnlyChangedFields(array $changeset): void
    {
        $entity = new ItemFilterEntity(name: 'TestFilter.filter', realm: 'pc');
        $before = self::getEntitySnapshot($entity);

        $entity->update($changeset);
        $after = self::getEntitySnapshot($entity);

        static::assertSame(array_replace($before, $changeset), $after);
    }

    /** @throws RuntimeException */
    #[Test]
    public function emptyUpdateHasNoEffect(): void
    {
        $entity = new ItemFilterEntity(name: 'TestFilter.filter', realm: 'pc');
        $before = self::getEntitySnapshot($entity);

        $entity->update([]);
        $after = self::getEntitySnapshot($entity);

        static::assertSame($before, $after);
    }

    /** @throws RuntimeException */
    #[Test]
    public function makePublic(): void
    {
        $entity = new ItemFilterEntity(name: 'TestFilter.filter', realm: 'pc');
        $entity->update(['public' => true]);

        static::assertTrue($entity->isPublic());
    }

    /** @throws RuntimeException */
    #[Test]
    public function failToMakePrivate(): void
    {
        $entity = new ItemFilterEntity(
            name: 'TestFilter.filter',
            realm: 'pc',
            public: true,
        );
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs(ItemFilterEntity::PUBLIC_LOCK);
        $entity->update(['public' => false]);
    }

    /** @throws RuntimeException */
    #[Test]
    public function remainPublicHasNoEffect(): void
    {
        $entity = new ItemFilterEntity(
            name: 'TestFilter.filter',
            realm: 'pc',
            public: true,
        );
        $entity->update(['public' => true]);
        static::assertTrue($entity->isPublic());
    }
}
