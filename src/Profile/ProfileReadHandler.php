<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\HandlerInterface;
use LaminasApiSample\Profile\Embedded\StreamEmbeddable;
use LaminasApiSample\Profile\Embedded\TwitchEmbeddable;
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
        $response = new HttpResponse();

        // TODO(Blake Simpson): Since there is currently no auth scope (ie.
        // there is only one profile to fetch by scope) the following will need
        // to change once account:profile scope exists
        $profile = $this->profileRepo->findOneBy([]);

        if (!$profile) {
            // TODO(Blake Simpson): Building a 404 should become a shared
            // function
            $response->setStatusCode(404);
            $response->setContent(json_encode(['error' => 'Not Found']));
            return $response;
        }

        $twitch = $this->map_twitch_fields($profile->getTwitchEmbeddable());

        // TODO(Blake Simpson): Building a 200 should become a shared function
        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'uuid' => $profile->getUuid(),
            'name' => $profile->getName(),
            'locale' => $profile->getLocale(),
            ...($twitch ? ['twitch' => $twitch] : []),
        ]));

        return $response;
    }

    /** @return null|array<string, mixed> */
    private function map_twitch_fields(?TwitchEmbeddable $twitchEmbeddable = null): ?array
    {
        if (!$twitchEmbeddable) {
            return null;
        }

        $stream = $this->mapStreamFields(
            $twitchEmbeddable->getStreamEmbeddable(),
        );

        return [
            'name' => $twitchEmbeddable->getName(),
            ...(!$stream ? [] : ['stream' => $stream]),
        ];
    }

    /** @return null|array<string, mixed> */
    private function mapStreamFields(?StreamEmbeddable $streamEmbeddable = null): ?array
    {
        if (!$streamEmbeddable) {
            return null;
        }

        $name = $streamEmbeddable->getName();
        $image = $streamEmbeddable->getImage();
        $status = $streamEmbeddable->getStatus();

        return [
            ...(!$name ? [] : ['name' => $name]),
            ...(!$image ? [] : ['image' => $image]),
            ...(!$status ? [] : ['status' => $status]),
        ];
    }
}
