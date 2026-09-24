<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use LaminasApiSample\Profile\Embedded\StreamEmbeddable;
use LaminasApiSample\Profile\Embedded\TwitchEmbeddable;

final class ProfileAdapter
{
    public function __construct(
        private readonly ProfileEntity $profile,
    ) {}

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
    public function mapResponse(): array
    {
        $twitch = $this->mapTwitchData($this->profile->getTwitch());

        return [
            'uuid' => $this->profile->getId(),
            'name' => $this->profile->getName(),
            'locale' => $this->profile->getLocale(),
            ...($twitch ? ['twitch' => $twitch] : []),
        ];
    }

    /**
     * @return null|array{
     *     name: string,
     *     stream?: array{name?: string, image?: string, status?: string},
     * }
     */
    private function mapTwitchData(?TwitchEmbeddable $data = null): ?array
    {
        $name = $data?->getName();
        if (!$data || !$name) {
            return null;
        }

        $stream = $this->mapStreamData($data->getStream());

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
    private function mapStreamData(?StreamEmbeddable $data = null): ?array
    {
        if (!$data) {
            return null;
        }

        $name = $data->getName();
        $image = $data->getImage();
        $status = $data->getStatus();

        return [
            ...($name ? ['name' => $name] : []),
            ...($image ? ['image' => $image] : []),
            ...($status ? ['status' => $status] : []),
        ];
    }
}
