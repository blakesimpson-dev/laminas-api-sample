<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile;

use LaminasApiSample\Domains\Profile\Embedded\StreamEmbeddable;
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
     *         stream?: array{name?: string, image?: string, status?: string},
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

    /**
     * @return null|array{
     *     name: string,
     *     stream?: array{name?: string, image?: string, status?: string},
     * }
     */
    private function mapTwitchData(?TwitchEmbeddable $embedded = null): ?array
    {
        $name = $embedded?->getName();
        if (!$embedded || !$name) {
            return null;
        }

        $stream = $this->mapStreamData($embedded->getStream());

        return [
            'name' => $name,
            ...(!$stream ? [] : ['stream' => $stream]),
        ];
    }

    /**
     * @return null|array{
     *     name?: string,
     *     image?: string,
     *     status?: string,
     * }
     */
    private function mapStreamData(?StreamEmbeddable $embedded = null): ?array
    {
        return $embedded ? array_filter($embedded->toArray()) : null;
    }
}
