<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use DomainException;
use Ramsey\Uuid\Uuid;

#[Entity(repositoryClass: ItemFilterRepository::class)]
#[Table(name: 'item_filter')]
final class ItemFilterEntity
{
    #[Column(type: 'guid'), Id]
    private readonly string $id;

    #[Column(name: 'filter_name')]
    private string $name;

    #[Column]
    private string $realm;

    #[Column(type: 'text', nullable: true)]
    private ?string $filter;

    #[Column]
    private string $description;

    #[Column]
    private string $version;

    #[Column]
    private string $type;

    #[Column]
    private bool $public;

    // @mago-expect lint:excessive-parameter-list
    public function __construct(
        string $name,
        string $realm,
        ?string $filter = null,
        string $description = '',
        string $version = '',
        string $type = 'Normal',
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

    /**
     * @param array{
     *     filter_name?: string,
     *     realm?: string,
     *     filter?: string,
     *     description?: string,
     *     version?: string,
     *     type?: string,
     *     public?: bool,
     * } $updated
     */
    public function update(array $updated): void
    {
        if (($updated['public'] ?? null) === false && $this->public) {
            throw new DomainException(
                'A public filter cannot be made private.',
            );
        }

        $this->name = $updated['filter_name'] ?? $this->name;
        $this->realm = $updated['realm'] ?? $this->realm;
        $this->filter = $updated['filter'] ?? $this->filter;
        $this->description = $updated['description'] ?? $this->description;
        $this->version = $updated['version'] ?? $this->version;
        $this->type = $updated['type'] ?? $this->type;
        $this->public = $updated['public'] ?? $this->public;
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
