<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\ItemFilter;

use LaminasApiSample\Domains\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\Domains\ItemFilter\ItemFilterEntity;
use LaminasApiSampleTest\Support\FixedTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemFilterAdapter::class), UsesClass(ItemFilterEntity::class)]
final class ItemFilterAdapterTest extends TestCase
{
    /** @throws PHPUnitException */
    #[Test]
    public function assertResponseKeyContract(): void
    {
        $adapter = new ItemFilterAdapter();
        $response = $adapter->mapResponse(new ItemFilterEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'TestFilter.filter',
            realm: 'pc',
        ));

        static::assertArrayNotHasKey(
            'created_at',
            array_keys($response),
            'created_at should be omitted',
        );
        static::assertArrayNotHasKey(
            'updated_at',
            array_keys($response),
            'updated_at should be omitted',
        );

        static::assertSame(
            [
                'id',
                'filter_name',
                'realm',
                'description',
                'version',
                'type',
                'public',
            ],
            array_keys($response),
        );
    }

    /** @throws PHPUnitException */
    #[Test]
    public function assertResponseContract(): void
    {
        $entity = new ItemFilterEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'TestFilter.filter',
            realm: 'pc',
            filter: 'Filter content',
            description: 'Description content',
            version: '8.20',
            type: 'Normal',
            public: false,
        );

        $adapter = new ItemFilterAdapter();
        $response = $adapter->mapResponse($entity);

        static::assertArrayNotHasKey(
            'created_at',
            array_keys($response),
            'created_at should be omitted',
        );
        static::assertArrayNotHasKey(
            'updated_at',
            array_keys($response),
            'updated_at should be omitted',
        );

        static::assertSame(
            [
                'id' => $entity->getId(),
                'filter_name' => 'TestFilter.filter',
                'realm' => 'pc',
                'filter' => 'Filter content',
                'description' => 'Description content',
                'version' => '8.20',
                'type' => 'Normal',
                'public' => false,
            ],
            $response,
        );
    }

    /** @throws PHPUnitException */
    #[Test]
    public function assertListResponseItemKeyContract(): void
    {
        $adapter = new ItemFilterAdapter();
        $response = $adapter->mapListResponseItem(new ItemFilterEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'TestFilter.filter',
            realm: 'pc',
        ));

        static::assertArrayNotHasKey(
            'created_at',
            array_keys($response),
            'created_at should be omitted',
        );
        static::assertArrayNotHasKey(
            'updated_at',
            array_keys($response),
            'updated_at should be omitted',
        );

        static::assertSame(
            [
                'id',
                'filter_name',
                'realm',
                'description',
                'version',
                'type',
                'public',
            ],
            array_keys($response),
        );

        static::assertArrayNotHasKey('filter', $response);
    }
}
