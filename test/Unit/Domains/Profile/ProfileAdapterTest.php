<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\Profile;

use LaminasApiSample\Domains\Profile\Embedded\StreamEmbeddable;
use LaminasApiSample\Domains\Profile\Embedded\TwitchEmbeddable;
use LaminasApiSample\Domains\Profile\ProfileAdapter;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use LaminasApiSampleTest\Support\FixedTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;

#[
    CoversClass(ProfileAdapter::class),
    UsesClass(ProfileEntity::class),
    UsesClass(TwitchEmbeddable::class),
    UsesClass(StreamEmbeddable::class),
]
final class ProfileAdapterTest extends TestCase
{
    /** @throws PHPUnitException */
    #[Test]
    public function assertResponseContract(): void
    {
        $entity = new ProfileEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'Profile name',
            locale: 'Profile locale',
            twitch: new TwitchEmbeddable(
                name: 'Twitch name',
                stream: new StreamEmbeddable(
                    name: 'Stream name',
                    image: 'Stream image',
                    status: 'Stream status',
                ),
            ),
        );

        $adapter = new ProfileAdapter();
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
                'uuid' => $entity->getId(),
                'name' => 'Profile name',
                'locale' => 'Profile locale',
                'twitch' => [
                    'name' => 'Twitch name',
                    'stream' => [
                        'name' => 'Stream name',
                        'image' => 'Stream image',
                        'status' => 'Stream status',
                    ],
                ],
            ],
            $response,
        );
    }

    /** @throws PHPUnitException */
    #[Test]
    public function assertLocaleOmission(): void
    {
        $entity = new ProfileEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'Profile name',
        );

        static::assertSame(
            ['uuid' => $entity->getId(), 'name' => 'Profile name'],
            new ProfileAdapter()->mapResponse($entity),
        );
    }

    /** @return iterable<string, array{?TwitchEmbeddable}> */
    public static function getDiscreteTwitchOmission(): iterable
    {
        yield 'twitch object omitted' => [null];
        yield 'twitch object name field omitted' => [new TwitchEmbeddable(
            name: null,
            stream: null,
        )];
        yield 'twitch object name empty' => [new TwitchEmbeddable(
            name: '',
            stream: null,
        )];
    }

    /** @throws PHPUnitException */
    #[Test, DataProvider('getDiscreteTwitchOmission')]
    public function assertTwitchOmission(?TwitchEmbeddable $twitch): void
    {
        $response = new ProfileAdapter()->mapResponse(new ProfileEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'Profile name',
            locale: 'Profile locale',
            twitch: $twitch,
        ));

        static::assertSame(['uuid', 'name', 'locale'], array_keys($response));
    }

    /** @return iterable<string, array{TwitchEmbeddable, array<string, mixed>}> */
    public static function getDiscreteTwitchInclusion(): iterable
    {
        yield 'twitch object included with stream object omitted' => [
            new TwitchEmbeddable(name: 'Twitch name', stream: null),
            ['name' => 'Twitch name'],
        ];

        yield 'twitch object included with empty stream object omitted' => [
            new TwitchEmbeddable(
                name: 'Twitch name',
                stream: new StreamEmbeddable(null, null, null),
            ),
            ['name' => 'Twitch name'],
        ];

        yield 'twitch object included with partial stream object included' => [
            new TwitchEmbeddable(
                name: 'Twitch name',
                stream: new StreamEmbeddable(null, null, 'live'),
            ),
            ['name' => 'Twitch name', 'stream' => ['status' => 'live']],
        ];

        yield 'full stream' => [
            new TwitchEmbeddable(
                name: 'Twitch name',
                stream: new StreamEmbeddable(
                    'Stream name',
                    'Stream image',
                    'Stream status',
                ),
            ),
            [
                'name' => 'Twitch name',
                'stream' => [
                    'name' => 'Stream name',
                    'image' => 'Stream image',
                    'status' => 'Stream status',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     * @throws PHPUnitException
     */
    #[Test, DataProvider('getDiscreteTwitchInclusion')]
    public function assertTwitchInclusion(
        TwitchEmbeddable $twitch,
        array $expected,
    ): void {
        $response = new ProfileAdapter()->mapResponse(new ProfileEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'Profile name',
            locale: 'Profile locale',
            twitch: $twitch,
        ));

        static::assertArrayHasKey('twitch', $response);
        static::assertSame($expected, $response['twitch'] ?? []);
    }
}
