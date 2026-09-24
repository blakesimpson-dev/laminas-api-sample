<?php

declare(strict_types=1);

namespace LaminasApiSample\ItemFilter;

use LaminasApiSample\HandlerType;

final class ItemFilterAdapter
{
    public function __construct(
        private readonly ItemFilterEntity $itemFilter,
    ) {}

    /**
     * @return array{
     *     id: string,
     *     filter_name: string,
     *     realm: string,
     *     filter: ?string,
     *     description: string,
     *     version: string,
     *     type: string,
     *     public: bool,
     * }
     */
    public function mapResponse(): array
    {
        return [
            'id' => $this->itemFilter->getId(),
            'filter_name' => $this->itemFilter->getName(),
            'realm' => $this->itemFilter->getRealm(),
            'filter' => $this->itemFilter->getFilter(),
            'description' => $this->itemFilter->getDescription(),
            'version' => $this->itemFilter->getVersion(),
            'type' => $this->itemFilter->getType(),
            'public' => $this->itemFilter->isPublic(),
        ];
    }

    /**
     * @return array{
     *     id: string,
     *     filter_name: string,
     *     realm: string,
     *     description: string,
     *     version: string,
     *     type: string,
     *     public: bool,
     * }
     */
    public function mapListResponseItem(): array
    {
        return [
            'id' => $this->itemFilter->getId(),
            'filter_name' => $this->itemFilter->getName(),
            'realm' => $this->itemFilter->getRealm(),
            'description' => $this->itemFilter->getDescription(),
            'version' => $this->itemFilter->getVersion(),
            'type' => $this->itemFilter->getType(),
            'public' => $this->itemFilter->isPublic(),
        ];
    }
}
