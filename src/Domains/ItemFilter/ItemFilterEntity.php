<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types as DoctrineDBTypes;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use LaminasApiSample\Domains\TimestampedEntity;
use Ramsey\Uuid\Uuid;

#[Entity(repositoryClass: ItemFilterRepository::class)]
#[Table(name: 'item_filter')]
final class ItemFilterEntity extends TimestampedEntity
{
    public const array REALMS = ['pc', 'xbox', 'sony', 'poe2'];
    public const array TYPES = ['Normal', 'Ruthless'];

    #[ManyToOne(targetEntity: ProfileEntity::class)]
    #[JoinColumn(
        name: 'profile_id',
        referencedColumnName: 'uuid',
        nullable: false,
    )]
    private readonly ProfileEntity $profile;

    #[Column(type: DoctrineDBTypes::GUID), Id]
    private readonly string $id;

    #[Column(name: 'filter_name')]
    private string $name;

    #[Column]
    private string $realm;

    #[Column(type: DoctrineDBTypes::TEXT, nullable: true)]
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
        DateTimeImmutable $createdAt,
        ProfileEntity $profile,
        string $name,
        string $realm,
        ?string $filter = null,
        string $description = '',
        string $version = '',
        string $type = 'Normal',
        bool $public = false,
    ) {
        parent::__construct($createdAt);
        $this->id = Uuid::uuid4()->toString();
        $this->profile = $profile;
        $this->name = $name;
        $this->realm = $realm;
        $this->filter = $filter;
        $this->description = $description;
        $this->version = $version;
        $this->type = $type;
        $this->public = $public;
    }

    public function update(ItemFilterPatch $patch, DateTimeImmutable $now): void
    {
        $before = get_object_vars($this);
        $this->name = $patch->name ?? $this->name;
        $this->realm = $patch->realm ?? $this->realm;
        $this->filter = $patch->filter ?? $this->filter;
        $this->description = $patch->description ?? $this->description;
        $this->version = $patch->version ?? $this->version;
        $this->type = $patch->type ?? $this->type;

        if (get_object_vars($this) !== $before) {
            $this->touch($now);
        }
    }

    public function publish(DateTimeImmutable $now): void
    {
        if ($this->public) {
            return;
        }

        $this->public = true;
        $this->touch($now);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProfile(): ProfileEntity
    {
        return $this->profile;
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
