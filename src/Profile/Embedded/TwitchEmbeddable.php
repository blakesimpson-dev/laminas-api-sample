<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile\Embedded;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping\Embedded;

#[Embeddable]
final class TwitchEmbeddable
{
    #[Embedded(class: StreamEmbeddable::class)]
    private readonly ?StreamEmbeddable $stream;

    public function __construct(
        #[Column(nullable: true)] private readonly ?string $name,
        ?StreamEmbeddable $stream,
    ) {
        $this->stream = $stream;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStreamEmbeddable(): ?StreamEmbeddable
    {
        return $this->stream;
    }
}
