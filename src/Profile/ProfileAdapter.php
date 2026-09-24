<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use LaminasApiSample\Profile\Embedded\StreamEmbeddable;
use LaminasApiSample\Profile\Embedded\TwitchEmbeddable;

final class ProfileAdapter
{
    /**
     * @return array{
     *     uuid: string,
     *     name: string,
     *     locale: ?string,
     *     twitch?: array{
     *         name: string,
     *         stream?: array{name?: string, image?: string, status?: string},
     *     },
     * }
     */
    public function mapResponse(ProfileEntity $entity): array
    {
        $twitch = $this->mapTwitchData($entity->getTwitch());

        return [
            'uuid' => $entity->getId(),
            'name' => $entity->getName(),
            'locale' => $entity->getLocale(),
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
        if (!$embedded) {
            return null;
        }

        $name = $embedded->getName();
        $image = $embedded->getImage();
        $status = $embedded->getStatus();

        return [
            ...($name ? ['name' => $name] : []),
            ...($image ? ['image' => $image] : []),
            ...($status ? ['status' => $status] : []),
        ];
    }
}
