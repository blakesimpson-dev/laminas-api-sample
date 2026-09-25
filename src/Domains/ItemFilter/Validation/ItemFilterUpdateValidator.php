<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter\Validation;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\InArray;
use Laminas\Validator\StringLength;
use LaminasApiSample\Domains\ItemFilter\ItemFilterEntity;

/**
 * @extends InputFilter<array{
 *     filter_name: ?string,
 *     realm: ?string,
 *     filter: ?string,
 *     description: ?string,
 *     version: ?string,
 *     type: ?string,
 *     public: ?bool,
 * }>
 */
final class ItemFilterUpdateValidator extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'filter_name',
            'required' => false,
            'filters' => [['name' => StringTrim::class]],
            'validators' => [[
                'name' => StringLength::class,
                'options' => ['min' => 1, 'max' => 255],
            ]],
        ]);

        $this->add([
            'name' => 'realm',
            'required' => false,
            'validators' => [[
                'name' => InArray::class,
                'options' => [
                    'haystack' => ItemFilterEntity::REALMS,
                    'strict' => InArray::COMPARE_STRICT,
                    'messages' => [
                        InArray::NOT_IN_ARRAY =>
                            'Must be one of: '
                                . implode(', ', ItemFilterEntity::REALMS),
                    ],
                ],
            ]],
        ]);

        $this->add([
            'name' => 'filter',
            'required' => false,
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
                    'haystack' => ItemFilterEntity::TYPES,
                    'strict' => InArray::COMPARE_STRICT,
                    'messages' => [
                        InArray::NOT_IN_ARRAY =>
                            'Must be one of: '
                                . implode(', ', ItemFilterEntity::TYPES),
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
                    'haystack' => [true],
                    'strict' => InArray::COMPARE_STRICT,
                    'messages' => [
                        InArray::NOT_IN_ARRAY => ItemFilterEntity::PUBLIC_LOCK,
                    ],
                ],
            ]],
        ]);
    }
}
