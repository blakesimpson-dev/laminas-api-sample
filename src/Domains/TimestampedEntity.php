<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
abstract class TimestampedEntity
{
    #[Column(name: 'created_at', type: Types::DATETIMETZ_IMMUTABLE)]
    private readonly DateTimeImmutable $createdAt;

    #[Column(
        name: 'updated_at',
        type: Types::DATETIMETZ_IMMUTABLE,
        nullable: true,
    )]
    private ?DateTimeImmutable $updatedAt = null;

    protected function __construct(DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    protected function touch(DateTimeImmutable $now): void
    {
        $this->updatedAt = $now;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
