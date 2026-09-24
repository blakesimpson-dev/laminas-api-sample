<?php

declare(strict_types=1);

namespace LaminasApiSample\ItemFilter\Handlers;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\HandlerInterface;
use LaminasApiSample\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\ItemFilter\ItemFilterRepository;
use LaminasApiSample\Router;
use Override;

final class ItemFilterReadManyHandler implements HandlerInterface
{
    public function __construct(
        private readonly ItemFilterRepository $repository,
        private readonly ItemFilterAdapter $adapter,
    ) {}

    /** @param ?array<string, string> $params */
    #[Override]
    public function __invoke(?array $params = null): HttpResponse
    {
        $entities = $this->repository->findAll();
        return Router::buildResponse([
            'filters' => array_map(
                $this->adapter->mapListResponseItem(...),
                $entities,
            ),
        ]);
    }
}
