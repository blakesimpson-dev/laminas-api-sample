<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
final class Stream
{
    public function __construct(
        #[Column(nullable: true)]
        public readonly string $name,

        #[Column(nullable: true)]
        public readonly string $image,

        #[Column(nullable: true)]
        public readonly string $status,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
