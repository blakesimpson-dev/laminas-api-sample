<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\Profile;

use LaminasApiSample\Domains\Profile\Embedded\TwitchEmbeddable;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use LaminasApiSampleTest\Support\FixedTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(ProfileEntity::class)]
final class ProfileEntityTest extends TestCase
{
    /** @throws PHPUnitException */
    #[Test]
    public function constructWithValidParamsAndDefaults(): void
    {
        $entity = new ProfileEntity(
            createdAt: FixedTime::getForCreate(),
            name: 'Profile name',
            locale: 'Profile locale',
            twitch: new TwitchEmbeddable(name: 'Twitch name'),
        );

        $twitch = $entity->getTwitch();
        if ($twitch === null) {
            static::fail('twitch should be set');
        }

        static::assertSame('Profile name', $entity->getName());
        static::assertSame('Profile locale', $entity->getLocale());
        static::assertSame('Twitch name', $twitch->getName());
        static::assertTrue(Uuid::isValid($entity->getId()));

        static::assertEquals(
            FixedTime::getForCreate(),
            $entity->getCreatedAt(),
            'created timestamp should be set',
        );
        static::assertNull(
            $entity->getUpdatedAt(),
            'updated timestamp should remain null',
        );
    }
}
