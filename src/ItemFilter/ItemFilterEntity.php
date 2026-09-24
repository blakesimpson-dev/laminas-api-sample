<?php

declare(strict_types=1);

namespace LaminasApiSample\ItemFilter;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use LaminasApiSample\Profile\Embedded\TwitchEmbeddable;
use Ramsey\Uuid\Uuid;

#[Entity(repositoryClass: ItemFilterRepository::class)]
#[Table(name: 'item_filter')]
final class ItemFilterEntity
{
    #[Column(type: 'guid'), Id]
    private readonly string $uuid;

    #[Column(name: 'filter_name')]
    private readonly string $filterName;

    // "realm": "string",
    // "description": "string",
    // "version": "string",
    // "type": "string",
    // "public": "?bool",
    // "filter": "?string",
    // "validation": "?object",
    // "↳valid": "bool",
    // "↳version": "?string",
    // "↳validated": "?string"
}
