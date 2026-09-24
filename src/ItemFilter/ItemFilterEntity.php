<?php

declare(strict_types=1);

namespace LaminasApiSample\ItemFilter;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Ramsey\Uuid\Uuid;

#[Entity(repositoryClass: ItemFilterRepository::class)]
#[Table(name: 'item_filter')]
final class ItemFilterEntity
{
    #[Column(type: 'guid'), Id]
    private readonly string $id;

    #[Column(name: 'filter_name')]
    private readonly string $name;

    #[Column]
    private readonly string $realm;

    #[Column(type: 'text', nullable: true)]
    private readonly ?string $filter;

    #[Column]
    private readonly string $description;

    #[Column]
    private readonly string $version;

    #[Column]
    private readonly string $type;

    #[Column]
    private readonly bool $public;

    // @mago-expect lint:excessive-parameter-list
    public function __construct(
        string $name,
        string $realm = 'pc',
        ?string $filter = null,
        string $description = '',
        string $version = '',
        string $type = 'Normale',
        bool $public = false,
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->name = $name;
        $this->realm = $realm;
        $this->filter = $filter;
        $this->description = $description;
        $this->version = $version;
        $this->type = $type;
        $this->public = $public;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRealm(): string
    {
        return $this->realm;
    }

    public function getFilter(): ?string
    {
        return $this->filter;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }
}
