<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter\Handlers;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\Domains\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\Domains\ItemFilter\ItemFilterRepository;
use LaminasApiSample\Http\HandlerInterface;
use LaminasApiSample\Http\JsonResponseFactory;
use Override;

final class ItemFilterReadHandler implements HandlerInterface
{
    public function __construct(
        private readonly ItemFilterRepository $repository,
        private readonly ItemFilterAdapter $adapter,
    ) {}

    /** @param array<string, string> $params */
    #[Override]
    public function __invoke(HttpRequest $request, array $params): HttpResponse
    {
        $id = $params['id'] ?? null;
        if (!$id) {
            return JsonResponseFactory::badRequest();
        }

        $entity = $this->repository->find($id);
        if (!$entity) {
            return JsonResponseFactory::notFound();
        }

        return JsonResponseFactory::ok([
            'filter' => $this->adapter->mapResponse($entity),
        ]);
    }
}
