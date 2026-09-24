<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter;

final class ItemFilterAdapter
{
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
    public function mapResponse(ItemFilterEntity $entity): array
    {
        return [
            'id' => $entity->getId(),
            'filter_name' => $entity->getName(),
            'realm' => $entity->getRealm(),
            'filter' => $entity->getFilter(),
            'description' => $entity->getDescription(),
            'version' => $entity->getVersion(),
            'type' => $entity->getType(),
            'public' => $entity->isPublic(),
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
    public function mapListResponseItem(ItemFilterEntity $entity): array
    {
        return [
            'id' => $entity->getId(),
            'filter_name' => $entity->getName(),
            'realm' => $entity->getRealm(),
            'description' => $entity->getDescription(),
            'version' => $entity->getVersion(),
            'type' => $entity->getType(),
            'public' => $entity->isPublic(),
        ];
    }
}
