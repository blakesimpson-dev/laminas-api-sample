<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use LaminasApiSample\Profile\Embedded\TwitchEmbeddable;
use Ramsey\Uuid\Uuid;

#[Entity(repositoryClass: ProfileRepository::class)]
#[Table(name: "profile")]
final class ProfileEntity
{
    #[Column(type: "guid"), Id]
    private readonly string $uuid;

    #[Column]
    private readonly string $name;

    #[Column(nullable: true)]
    private readonly ?string $locale;

    #[Embedded(class: TwitchEmbeddable::class)]
    private readonly ?TwitchEmbeddable $twitch;

    public function __construct(
        string $name,
        ?string $locale,
        ?TwitchEmbeddable $twitch,
    ) {
        $this->uuid = Uuid::uuid4()->toString();
        $this->name = $name;
        $this->locale = $locale;
        $this->twitch = $twitch;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getTwitchEmbeddable(): ?TwitchEmbeddable
    {
        return $this->twitch;
    }
}
