<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\Profile;

use LaminasApiSample\Domains\Profile\Embedded\StreamEmbeddable;
use LaminasApiSample\Domains\Profile\Embedded\TwitchEmbeddable;
use LaminasApiSample\Domains\Profile\ProfileAdapter;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[
    CoversClass(ProfileAdapter::class),
    UsesClass(ProfileEntity::class),
    UsesClass(TwitchEmbeddable::class),
    UsesClass(StreamEmbeddable::class),
]
final class ProfileAdapterTest extends TestCase
{
    /** @throws RuntimeException */
    #[Test]
    public function assertResponseContract(): void
    {
        $stream = new StreamEmbeddable(
            name: 'Stream name',
            image: 'Stream image',
            status: 'Stream status content',
        );
        $twitch = new TwitchEmbeddable(name: 'Twitch name', stream: $stream);
        $entity = new ProfileEntity(
            name: 'Profile name',
            locale: 'Profile locale',
            twitch: $twitch,
        );

        $adapter = new ProfileAdapter();
        $response = $adapter->mapResponse($entity);

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
                        'status' => 'Stream status content',
                    ],
                ],
            ],
            $response,
        );
    }

    /** @throws RuntimeException */
    #[Test]
    public function assertLocaleOmission(): void
    {
        $entity = new ProfileEntity(name: 'Profile name');

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
            null,
            null,
        )];
        yield 'twitch object name empty' => [new TwitchEmbeddable('', null)];
    }

    /** @throws RuntimeException */
    #[Test, DataProvider('getDiscreteTwitchOmission')]
    public function assertTwitchOmission(?TwitchEmbeddable $twitch): void
    {
        $response = new ProfileAdapter()->mapResponse(new ProfileEntity(
            'Profile name',
            'Profile locale',
            $twitch,
        ));

        static::assertSame(['uuid', 'name', 'locale'], array_keys($response));
    }

    /** @return iterable<string, array{TwitchEmbeddable, array<string, mixed>}> */
    public static function getDiscreteTwitchInclusion(): iterable
    {
        yield 'twitch object included with stream object omitted' => [
            new TwitchEmbeddable('Twitch name', null),
            ['name' => 'Twitch name'],
        ];

        yield 'twitch object included with empty stream object omitted' => [
            new TwitchEmbeddable(
                'Twitch name',
                new StreamEmbeddable(null, null, null),
            ),
            ['name' => 'Twitch name'],
        ];

        yield 'twitch object included with partial stream object included' => [
            new TwitchEmbeddable(
                'Twitch name',
                new StreamEmbeddable(null, null, 'live'),
            ),
            ['name' => 'Twitch name', 'stream' => ['status' => 'live']],
        ];

        yield 'full stream' => [
            new TwitchEmbeddable(
                'Twitch name',
                new StreamEmbeddable(
                    'Stream name',
                    'Stream image',
                    'Stream status content',
                ),
            ),
            [
                'name' => 'Twitch name',
                'stream' => [
                    'name' => 'Stream name',
                    'image' => 'Stream image',
                    'status' => 'Stream status content',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     * @throws RuntimeException
     */
    #[Test, DataProvider('getDiscreteTwitchInclusion')]
    public function assertTwitchInclusion(
        TwitchEmbeddable $twitch,
        array $expected,
    ): void {
        $response = new ProfileAdapter()->mapResponse(new ProfileEntity(
            'Profile name',
            'Profile locale',
            $twitch,
        ));

        static::assertArrayHasKey('twitch', $response);
        static::assertSame($expected, $response['twitch'] ?? []);
    }
}
