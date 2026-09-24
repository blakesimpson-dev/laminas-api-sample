<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\HandlerInterface;
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

        $adapter = new ProfileAdapter($profile);
        return Router::buildResponse($adapter->mapResponse());
    }
}
