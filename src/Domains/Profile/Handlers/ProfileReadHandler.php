<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile\Handlers;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\Domains\Profile\ProfileAdapter;
use LaminasApiSample\Domains\Profile\ProfileRepository;
use LaminasApiSample\Http\HandlerInterface;
use LaminasApiSample\Http\JsonResponseFactory;
use Override;

final class ProfileReadHandler implements HandlerInterface
{
    public function __construct(
        private readonly ProfileRepository $repository,
        private readonly ProfileAdapter $adapter,
    ) {}

    /** @param array<string, string> $params */
    #[Override]
    public function __invoke(HttpRequest $request, array $params): HttpResponse
    {
        $entity = $this->repository->findOneBy([]);
        if (!$entity) {
            return JsonResponseFactory::notFound();
        }

        return JsonResponseFactory::ok($this->adapter->mapResponse($entity));
    }
}
