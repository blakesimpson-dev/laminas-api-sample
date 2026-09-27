<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Contract;

use JsonException;
use LaminasApiSample\Domains\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\Domains\ItemFilter\ItemFilterEntity;
use LaminasApiSample\Domains\Profile\ProfileAdapter;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use LaminasApiSample\Domains\Profile\TwitchEmbeddable;
use LaminasApiSampleTest\Support\FixedTime;
use LaminasApiSampleTest\Support\TestProfile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;

#[
    CoversClass(ItemFilterAdapter::class),
    CoversClass(ProfileAdapter::class),
    UsesClass(ItemFilterEntity::class),
    UsesClass(ProfileEntity::class),
    UsesClass(TwitchEmbeddable::class),
]
final class OpenApiContractTest extends TestCase
{
    private const string SPEC_PATH = '/docs/openapi.json';

    /**
     * @return array{
     *      required?: list<string>,
     *      properties?: array<string, array<string, mixed>>
     *  }
     * @throws JsonException
     * @throws PHPUnitException
     */
    private static function schema(string $name): array
    {
        $json = file_get_contents(dirname(__DIR__, 3) . self::SPEC_PATH);
        if ($json === false) {
            static::fail('docs/openapi.json could not be read');
        }

        /**
         * @var array{
         *      components: array{
         *          schemas: array<string, array{
         *              required?: list<string>,
         *              properties?: array<string, array<string, mixed>>,
         *          }>,
         *      },
         *  } $spec
         */
        $spec = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        return $spec['components']['schemas'][$name] ?? static::fail(
            "Schema {$name} is missing from the spec",
        );
    }

    private static function fullItemFilter(): ItemFilterEntity
    {
        return new ItemFilterEntity(
            createdAt: FixedTime::inThePast(),
            profile: TestProfile::new(),
            name: 'Contract.filter',
            realm: 'pc',
            filter: 'Show',
            description: 'Description',
            version: '1.0',
            type: 'Normal',
            public: true,
        );
    }

    private static function minimalItemFilter(): ItemFilterEntity
    {
        return new ItemFilterEntity(
            createdAt: FixedTime::inThePast(),
            profile: TestProfile::new(),
            name: 'Contract.filter',
            realm: 'pc',
        );
    }

    private static function fullProfile(): ProfileEntity
    {
        return new ProfileEntity(
            createdAt: FixedTime::inThePast(),
            name: 'ContractProfile',
            locale: 'en_AU',
            twitch: new TwitchEmbeddable(name: 'ContractTwitch'),
        );
    }

    private static function minimalProfile(): ProfileEntity
    {
        return new ProfileEntity(
            createdAt: FixedTime::inThePast(),
            name: 'ContractProfile',
        );
    }

    /** @return iterable<string, array{string, array<string, mixed>}> */
    public static function getAdapterOutputs(): iterable
    {
        $itemFilters = new ItemFilterAdapter();
        $profiles = new ProfileAdapter();

        yield 'ItemFilter, full' => [
            'ItemFilter',
            $itemFilters->mapResponse(self::fullItemFilter()),
        ];
        yield 'ItemFilter, minimal' => [
            'ItemFilter',
            $itemFilters->mapResponse(self::minimalItemFilter()),
        ];
        yield 'ItemFilterListItem, full' => [
            'ItemFilterListItem',
            $itemFilters->mapListResponseItem(self::fullItemFilter()),
        ];
        yield 'Profile, full' => [
            'Profile',
            $profiles->mapResponse(self::fullProfile()),
        ];
        yield 'Profile, minimal' => [
            'Profile',
            $profiles->mapResponse(self::minimalProfile()),
        ];
    }

    /**
     * @param array<string, mixed> $output
     * @throws JsonException
     * @throws PHPUnitException
     */
    #[Test, DataProvider('getAdapterOutputs')]
    public function adapterOutputMatchesSchema(
        string $schemaName,
        array $output,
    ): void {
        self::assertMatchesSchema(
            self::schema($schemaName),
            $output,
            $schemaName,
        );
    }

    /**
     * @param array{
     *      required?: list<string>,
     *      properties?: array<string, array<string, mixed>>
     *  } $schema
     * @param array<array-key, mixed> $output
     * @throws PHPUnitException
     */
    private static function assertMatchesSchema(
        array $schema,
        array $output,
        string $path,
    ): void {
        $required = $schema['required'] ?? [];
        $properties = $schema['properties'] ?? [];
        $outputKeys = array_map('strval', array_keys($output));

        static::assertSame(
            [],
            array_values(array_diff($required, $outputKeys)),
            "{$path}: required keys missing from the adapter output",
        );
        static::assertSame(
            [],
            array_values(array_diff($outputKeys, array_keys($properties))),
            "{$path}: adapter output has keys the spec doesn't list",
        );

        foreach ($properties as $key => $property) {
            /**
             * @var array{
             *      required?: list<string>,
             *      properties?: array<string, array<string, mixed>>
             *  } $propertySchema
             */
            $propertySchema = $property;

            /** @var array<array-key, mixed>|scalar|null $value */
            $value = $output[$key] ?? null;
            if (
                !is_array($value)
                || !array_key_exists('properties', $propertySchema)
            ) {
                continue;
            }

            self::assertMatchesSchema(
                $propertySchema,
                $value,
                "{$path}.{$key}",
            );
        }
    }
}
