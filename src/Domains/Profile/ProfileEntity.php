<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use LaminasApiSample\Domains\Profile\Embedded\TwitchEmbeddable;
use Ramsey\Uuid\Uuid;

#[Entity(repositoryClass: ProfileRepository::class)]
#[Table(name: 'profile')]
final class ProfileEntity
{
    #[Column(name: 'uuid', type: 'guid'), Id]
    private readonly string $id;

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
        $this->id = Uuid::uuid4()->toString();
        $this->name = $name;
        $this->locale = $locale;
        $this->twitch = $twitch;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getTwitch(): ?TwitchEmbeddable
    {
        return $this->twitch;
    }
}
