<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile\Embedded;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
final class StreamEmbeddable
{
    public function __construct(
        #[Column(nullable: true)]
        private readonly ?string $name,
        #[Column(nullable: true)]
        private readonly ?string $image,
        #[Column(nullable: true)]
        private readonly ?string $status,
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }
}
