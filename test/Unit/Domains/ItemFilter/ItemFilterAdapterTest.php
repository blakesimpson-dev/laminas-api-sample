<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\ItemFilter;

use LaminasApiSample\Domains\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\Domains\ItemFilter\ItemFilterEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(ItemFilterAdapter::class), UsesClass(ItemFilterEntity::class)]
final class ItemFilterAdapterTest extends TestCase
{
    /** @throws RuntimeException */
    #[Test]
    public function assertResponseKeyContract(): void
    {
        $adapter = new ItemFilterAdapter();
        $response = $adapter->mapResponse(new ItemFilterEntity(
            'TestFilter.filter',
            'pc',
        ));

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

    /** @throws RuntimeException */
    #[Test]
    public function assertResponseContract(): void
    {
        $entity = new ItemFilterEntity(
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

    /** @throws RuntimeException */
    #[Test]
    public function assertListResponseItemKeyContract(): void
    {
        $adapter = new ItemFilterAdapter();
        $response = $adapter->mapListResponseItem(new ItemFilterEntity(
            'TestFilter.filter',
            'pc',
        ));

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
