<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\AbstractHandler;
use LaminasApiSample\Profile\Embedded\StreamEmbeddable;
use LaminasApiSample\Profile\Embedded\TwitchEmbeddable;
use Override;

final class ProfileReadHandler extends AbstractHandler
{
    public function __construct(
        private readonly ProfileRepository $profileRepository,
    ) {}

    /** @param ?array<array-key, mixed> $params */
    #[Override]
    public function __invoke(?array $params = null): HttpResponse
    {
        $response = new HttpResponse();

        // TODO(Blake): Since there is currently no auth scope (ie. there is
        // only one profile to fetch by scope) the following will need to change
        // once account:profile scope exists
        $profile = $this->profileRepository->findOneBy([]);

        if (!$profile) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["error" => "Not Found"]));
            return $response;
        }

        $twitch = $this->mapTwitch($profile->getTwitchEmbeddable());

        $response->setStatusCode(200);
        $response->setContent(
            json_encode([
                "uuid" => $profile->getUuid(),
                "name" => $profile->getName(),
                "locale" => $profile->getLocale(),
                ...$twitch ? ["twitch" => $twitch] : [],
            ]),
        );

        return $response;
    }

    /** @return null|array<string, mixed> */
    private function mapTwitch(
        ?TwitchEmbeddable $twitchEmbeddable = null,
    ): ?array {
        if (!$twitchEmbeddable) {
            return null;
        }

        $stream = $this->mapStream($twitchEmbeddable->getStreamEmbeddable());

        return [
            "name" => $twitchEmbeddable->getName(),
            ...!$stream ? [] : ["stream" => $stream],
        ];
    }

    /** @return null|array<string, mixed> */
    private function mapStream(
        ?StreamEmbeddable $streamEmbeddable = null,
    ): ?array {
        if (!$streamEmbeddable) {
            return null;
        }

        $name = $streamEmbeddable->getName();
        $image = $streamEmbeddable->getImage();
        $status = $streamEmbeddable->getStatus();

        return [
            ...!$name ? [] : ["name" => $name],
            ...!$image ? [] : ["image" => $image],
            ...!$status ? [] : ["status" => $status],
        ];
    }
}
