<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile;

use LaminasApiSample\Domains\Profile\Embedded\TwitchEmbeddable;

final class ProfileAdapter
{
    /**
     * @return array{
     *     uuid: string,
     *     name: string,
     *     locale?: string,
     *     twitch?: array{
     *         name: string,
     *     },
     *  }
     */
    public function mapResponse(ProfileEntity $entity): array
    {
        $locale = $entity->getLocale();
        $twitch = $this->mapTwitchData($entity->getTwitch());

        return [
            'uuid' => $entity->getId(),
            'name' => $entity->getName(),
            ...($locale ? ['locale' => $locale] : []),
            ...($twitch ? ['twitch' => $twitch] : []),
        ];
    }

    /** @return null|array{name: string} */
    private function mapTwitchData(?TwitchEmbeddable $embedded = null): ?array
    {
        $name = $embedded?->getName();
        if (!$embedded || !$name) {
            return null;
        }

        return ['name' => $name];
    }
}
