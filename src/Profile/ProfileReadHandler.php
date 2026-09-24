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
        private readonly ProfileRepository $repository,
        private readonly ProfileAdapter $adapter,
    ) {}

    /** @param ?array<string, string> $params */
    #[Override]
    public function __invoke(?array $params = null): HttpResponse
    {
        $entity = $this->repository->findOneBy([]);
        if (!$entity) {
            return Router::buildNotFoundResponse();
        }

        return Router::buildResponse($this->adapter->mapResponse($entity));
    }
}
