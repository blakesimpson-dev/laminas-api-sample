<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
final class TwitchEmbeddable
{
    public function __construct(
        #[Column(nullable: true)]
        private readonly ?string $name,
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }
}
