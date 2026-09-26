<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\ItemFilter\Validation;

use Laminas\InputFilter\Exception\RuntimeException as InputFilterException;
use Laminas\Validator\InArray;
use LaminasApiSample\Domains\ItemFilter\ItemFilterPatch;
use LaminasApiSample\Domains\ItemFilter\Validation\ItemFilterUpdateValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemFilterUpdateValidator::class)]
final class ItemFilterUpdateValidatorTest extends TestCase
{
    /**
     * @throws PHPUnitException
     * @throws InputFilterException
     */
    #[Test]
    public function updateWithNoChanges(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData([]);

        static::assertTrue($validator->isValid());
    }

    /**
     * @throws PHPUnitException
     * @throws InputFilterException
     */
    #[Test]
    public function updateWithNewDescriptionOnly(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData(['description' => 'Description content']);

        static::assertTrue($validator->isValid());
    }

    /**
     * @throws PHPUnitException
     * @throws InputFilterException
     */
    #[Test]
    public function makePublic(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData(['public' => true]);

        static::assertTrue($validator->isValid());
    }

    /**
     * @throws PHPUnitException
     * @throws InputFilterException
     */
    #[Test]
    public function failToMakePrivate(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData(['public' => false]);

        static::assertFalse($validator->isValid());

        $notInArrayMessage = 'Public filters cannot be made private';
        static::assertSame(
            ['public' => [
                InArray::NOT_IN_ARRAY => $notInArrayMessage,
            ]],
            $validator->getMessages(),
        );
    }

    /**
     * @throws PHPUnitException
     * @throws InputFilterException
     */
    #[Test]
    public function failToUpdateWithInvalidRealm(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData(['realm' => 'PC']);

        static::assertFalse($validator->isValid());
        static::assertSame(['realm'], array_keys($validator->getMessages()));
    }

    /**
     * @throws PHPUnitException
     * @throws InputFilterException
     */
    #[Test]
    public function patchTranslatesApiNamesAndLeavesUnsentNull(): void
    {
        $validator = new ItemFilterUpdateValidator();
        $validator->setData([
            'filter_name' => 'Renamed.filter',
            'realm' => 'xbox',
        ]);

        static::assertTrue($validator->isValid());
        static::assertEquals(
            new ItemFilterPatch(name: 'Renamed.filter', realm: 'xbox'),
            $validator->getPatch(),
        );
    }

    /**
     * @throws PHPUnitException
     * @throws InputFilterException
     */
    #[Test]
    public function publishRequestedOnlyWhenPublicTrue(): void
    {
        $validator = new ItemFilterUpdateValidator();

        $validator->setData(['public' => true]);
        static::assertTrue($validator->isValid());
        static::assertTrue($validator->isPublishRequested());

        $validator->setData([]);
        static::assertTrue($validator->isValid());
        static::assertFalse($validator->isPublishRequested());
    }
}
