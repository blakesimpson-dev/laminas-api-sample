<?php

declare(strict_types=1);

namespace LaminasApiSample\ItemFilter\Handlers;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\HandlerInterface;
use LaminasApiSample\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\ItemFilter\ItemFilterRepository;
use LaminasApiSample\Router;
use Override;

final class ItemFilterReadHandler implements HandlerInterface
{
    public function __construct(
        private readonly ItemFilterRepository $repository,
        private readonly ItemFilterAdapter $adapter,
    ) {}

    /** @param ?array<string, string> $params */
    #[Override]
    public function __invoke(?array $params = null): HttpResponse
    {
        $id = $params['id'] ?? null;
        if (!$id) {
            return Router::buildBadRequestResponse();
        }

        $entity = $this->repository->find($id);
        if (!$entity) {
            return Router::buildNotFoundResponse();
        }

        return Router::buildResponse($this->adapter->mapResponse($entity));
    }
}
