<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping\Embedded;

#[Embeddable]
final class Twitch
{
    public function __construct(
        #[Column]
        public readonly string $name,

        #[Embedded(class: Stream::class)]
        public readonly ?Stream $stream,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getStream(): ?Stream
    {
        return $this->stream;
    }
}
