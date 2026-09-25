<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter;

final class ItemFilterPatch
{
    // @mago-expect lint:excessive-parameter-list
    public function __construct(
        public ?string $name = null,
        public ?string $realm = null,
        public ?string $filter = null,
        public ?string $description = null,
        public ?string $version = null,
        public ?string $type = null,
        public ?bool $public = null,
    ) {}
}
