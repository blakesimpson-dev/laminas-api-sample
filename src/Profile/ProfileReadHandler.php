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

        $twitch = $this->mapTwitchData($profile->getTwitchEmbeddable());
        return Router::buildResponse([
            'uuid' => $profile->getUuid(),
            'name' => $profile->getName(),
            'locale' => $profile->getLocale(),
            ...($twitch ? ['twitch' => $twitch] : []),
        ]);
    }

    /** @return null|array<string, mixed> */
    private function mapTwitchData(?TwitchEmbeddable $embedded = null): ?array
    {
        if (!$embedded || !$embedded->getName()) {
            return null;
        }

        $stream = $this->mapStreamData($embedded->getStreamEmbeddable());

        return [
            'name' => $embedded->getName(),
            ...(!$stream ? [] : ['stream' => $stream]),
        ];
    }

    /** @return null|array<string, mixed> */
    private function mapStreamData(?StreamEmbeddable $embedded = null): ?array
    {
        if (!$embedded) {
            return null;
        }

        $name = $embedded->getName();
        $image = $embedded->getImage();
        $status = $embedded->getStatus();

        return [
            ...(!$name ? [] : ['name' => $name]),
            ...(!$image ? [] : ['image' => $image]),
            ...(!$status ? [] : ['status' => $status]),
        ];
    }
}
