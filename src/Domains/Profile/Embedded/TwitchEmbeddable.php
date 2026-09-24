<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile\Embedded;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping\Embedded;

#[Embeddable]
final class TwitchEmbeddable
{
    public function __construct(
        #[Column(nullable: true)]
        private readonly ?string $name,
        #[Embedded(class: StreamEmbeddable::class)]
        private readonly ?StreamEmbeddable $stream,
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStream(): ?StreamEmbeddable
    {
        return $this->stream;
    }
}
