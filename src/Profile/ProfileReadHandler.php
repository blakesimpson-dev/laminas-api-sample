<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\HandlerInterface;
use LaminasApiSample\Profile\Embedded\StreamEmbeddable;
use LaminasApiSample\Profile\Embedded\TwitchEmbeddable;
use LaminasApiSample\Router;
use Override;

final class ProfileReadHandler implements HandlerInterface
{
    public function __construct(
        private readonly ProfileRepository $profileRepo,
    ) {}

    /** @param ?array<array-key, mixed> $params */
    #[Override]
    public function __invoke(?array $params = null): HttpResponse
    {
        $profile = $this->profileRepo->findOneBy([]);
        if (!$profile) {
            return Router::buildNotFoundResponse();
        }

        return Router::buildResponse($this->mapResponseData($profile));
    }

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
    private function mapResponseData(ProfileEntity $profile): array
    {
        $twitch = $this->mapTwitchData($profile->getTwitch());

        return [
            'uuid' => $profile->getId(),
            'name' => $profile->getName(),
            'locale' => $profile->getLocale(),
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
            ...(!$name ? [] : ['name' => $name]),
            ...(!$image ? [] : ['image' => $image]),
            ...(!$status ? [] : ['status' => $status]),
        ];
    }
}
