<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter\Validation;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\InArray;
use Laminas\Validator\StringLength;

/**
 * @extends InputFilter<array{
 *     filter_name: string,
 *     realm: string,
 *     filter: string,
 *     description: ?string,
 *     version: ?string,
 *     type: ?string,
 *     public: ?bool,
 * }>
 */
final class ItemFilterCreateValidator extends InputFilter
{
    private const array REALMS = ['pc', 'xbox', 'sony', 'poe2'];
    private const array TYPES = ['Normal', 'Ruthless'];

    public function __construct()
    {
        $this->add([
            'name' => 'filter_name',
            'required' => true,
            'filters' => [['name' => StringTrim::class]],
            'validators' => [[
                'name' => StringLength::class,
                'options' => ['min' => 1, 'max' => 255],
            ]],
        ]);

        $this->add([
            'name' => 'realm',
            'required' => true,
            'validators' => [[
                'name' => InArray::class,
                'options' => [
                    'haystack' => ['pc', 'xbox', 'sony', 'poe2'],
                    'strict' => InArray::COMPARE_STRICT,
                    'messages' => [
                        InArray::NOT_IN_ARRAY =>
                            'Must be one of: ' . implode(', ', self::REALMS),
                    ],
                ],
            ]],
        ]);

        $this->add([
            'name' => 'filter',
            'required' => true,
            'validators' => [[
                'name' => StringLength::class,
                'options' => ['min' => 1],
            ]],
        ]);

        $this->add([
            'name' => 'description',
            'required' => false,
            'validators' => [[
                'name' => StringLength::class,
                'options' => ['max' => 255],
            ]],
        ]);

        $this->add([
            'name' => 'version',
            'required' => false,
            'validators' => [[
                'name' => StringLength::class,
                'options' => ['max' => 255],
            ]],
        ]);

        $this->add([
            'name' => 'type',
            'required' => false,
            'validators' => [[
                'name' => InArray::class,
                'options' => [
                    'haystack' => ['Normal', 'Ruthless'],
                    'strict' => InArray::COMPARE_STRICT,
                    'messages' => [
                        InArray::NOT_IN_ARRAY =>
                            'Must be one of: ' . implode(', ', self::TYPES),
                    ],
                ],
            ]],
        ]);

        $this->add([
            'name' => 'public',
            'required' => false,
            'continue_if_empty' => true,
            'validators' => [[
                'name' => InArray::class,
                'options' => [
                    'haystack' => [true, false],
                    'strict' => InArray::COMPARE_STRICT,
                    'messages' => [
                        InArray::NOT_IN_ARRAY => 'Must be true or false',
                    ],
                ],
            ]],
        ]);
    }
}
