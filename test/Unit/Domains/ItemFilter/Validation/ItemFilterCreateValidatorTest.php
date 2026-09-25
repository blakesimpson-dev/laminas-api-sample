<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Domains\ItemFilter\Validation;

use Laminas\Validator\InArray;
use LaminasApiSample\Domains\ItemFilter\Validation\ItemFilterCreateValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(ItemFilterCreateValidator::class)]
final class ItemFilterCreateValidatorTest extends TestCase
{
    /** @return array<string, mixed> */
    private static function getMinimalValidBody(): array
    {
        return [
            'filter_name' => 'TestFilter.filter',
            'realm' => 'pc',
            'filter' => 'Filter content',
        ];
    }

    /** @return array<string, mixed> */
    private static function getPopulatedValidBody(): array
    {
        return [
            ...self::getMinimalValidBody(),
            'description' => 'Description content',
            'version' => '8.20',
            'type' => 'Normal',
            'public' => false,
        ];
    }

    /** @return iterable<string, array{array<string, mixed>, string}> */
    public static function getInvalidBodies(): iterable
    {
        $body = self::getMinimalValidBody();

        yield 'missing value for filter_name' => [
            array_diff_key($body, ['filter_name' => true]),
            'filter_name',
        ];

        yield 'invalid value for filter_name (empty string)' => [
            ['filter_name' => '    '] + $body,
            'filter_name',
        ];

        yield 'invalid value for realm (not an option)' => [
            ['realm' => 'australia'] + $body,
            'realm',
        ];

        yield 'invalid value for realm (case sensitive)' => [
            ['realm' => 'PC'] + $body,
            'realm',
        ];

        yield 'missing value for realm' => [
            array_diff_key($body, ['realm' => true]),
            'realm',
        ];

        yield 'missing value for filter' => [
            array_diff_key($body, ['filter' => true]),
            'filter',
        ];

        yield 'invalid value for type (not an option)' => [
            ['type' => 'Hardcore'] + $body,
            'type',
        ];

        yield 'invalid value for type (case sensitive)' => [
            ['type' => 'RUTHLESS'] + $body,
            'type',
        ];

        yield 'invalid value for public (not an option)' => [
            ['public' => 'yes'] + $body,
            'public',
        ];

        yield 'invalid value for public (unsupported character)' => [
            ['public' => 1] + $body,
            'public',
        ];

        yield 'invalid value for description (over 255 characters)' => [
            ['description' => str_repeat('a', 256)] + $body,
            'description',
        ];
    }

    /** @throws RuntimeException */
    #[Test]
    public function createWithMinimalValidBody(): void
    {
        $validator = new ItemFilterCreateValidator();
        $validator->setData(self::getMinimalValidBody());

        static::assertTrue($validator->isValid());
    }

    /** @throws RuntimeException */
    #[Test]
    public function createWithPopulatedValidBody(): void
    {
        $validator = new ItemFilterCreateValidator();
        $validator->setData(self::getPopulatedValidBody());

        static::assertTrue($validator->isValid());
    }

    /** @throws RuntimeException */
    #[Test]
    public function createAsPublic(): void
    {
        $validator = new ItemFilterCreateValidator();
        $validator->setData(['public' => true] + self::getPopulatedValidBody());

        static::assertTrue($validator->isValid());
    }

    /**
     * @param array<string, mixed> $body
     * @throws RuntimeException
     */
    #[Test, DataProvider('getInvalidBodies')]
    public function failToCreateWithInvalidField(
        array $body,
        string $field,
    ): void {
        $validator = new ItemFilterCreateValidator();
        $validator->setData($body);

        static::assertFalse($validator->isValid());
        static::assertSame([$field], array_keys($validator->getMessages()));
    }

    /** @throws RuntimeException */
    #[Test]
    public function failToCreateWithInvalidRealmListsAllowedValues(): void
    {
        $validator = new ItemFilterCreateValidator();
        $validator->setData(array_replace(self::getMinimalValidBody(), [
            'realm' => 'australia',
        ]));

        static::assertFalse($validator->isValid());
        static::assertSame(
            ['realm' => [
                InArray::NOT_IN_ARRAY => 'Must be one of: pc, xbox, sony, poe2',
            ]],
            $validator->getMessages(),
        );
    }
}
