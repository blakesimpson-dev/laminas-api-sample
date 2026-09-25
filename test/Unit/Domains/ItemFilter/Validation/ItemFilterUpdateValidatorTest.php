<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\ItemFilter\Validation;

use Laminas\Validator\InArray;
use LaminasApiSample\Domains\ItemFilter\ItemFilterEntity;
use LaminasApiSample\Domains\ItemFilter\Validation\ItemFilterUpdateValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(ItemFilterUpdateValidator::class)]
final class ItemFilterUpdateValidatorTest extends TestCase
{
    /** @throws RuntimeException */
    #[Test]
    public function updateWithNoChanges(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData([]);

        static::assertTrue($validator->isValid());
    }

    /** @throws RuntimeException */
    #[Test]
    public function updateWithNewDescriptionOnly(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData(['description' => 'Description content']);

        static::assertTrue($validator->isValid());
    }

    /** @throws RuntimeException */
    #[Test]
    public function makePublic(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData(['public' => true]);

        static::assertTrue($validator->isValid());
    }

    /** @throws RuntimeException */
    #[Test]
    public function failToMakePrivate(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData(['public' => false]);

        static::assertFalse($validator->isValid());

        static::assertSame(
            ['public' => [
                InArray::NOT_IN_ARRAY => ItemFilterEntity::PUBLIC_LOCK,
            ]],
            $validator->getMessages(),
        );
    }

    /** @throws RuntimeException */
    #[Test]
    public function failToUpdateWithInvalidRealm(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData(['realm' => 'PC']);

        static::assertFalse($validator->isValid());
        static::assertSame(['realm'], array_keys($validator->getMessages()));
    }
}
