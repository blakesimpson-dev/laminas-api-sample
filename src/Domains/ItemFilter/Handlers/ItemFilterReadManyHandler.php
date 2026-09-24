<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter\Handlers;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\Domains\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\Domains\ItemFilter\ItemFilterRepository;
use LaminasApiSample\Http\HandlerInterface;
use LaminasApiSample\Http\JsonResponseFactory;
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
        return JsonResponseFactory::ok([
            'filters' => array_map(
                $this->adapter->mapListResponseItem(...),
                $entities,
            ),
        ]);
    }
}
