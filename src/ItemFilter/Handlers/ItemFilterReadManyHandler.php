<?php

declare(strict_types=1);

namespace LaminasApiSample\ItemFilter\Handlers;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\HandlerInterface;
use Override;

final class ItemFilterReadManyHandler implements HandlerInterface
{
    /** @param ?array<array-key, mixed> $params */
    #[Override]
    public function __invoke(?array $params = null): HttpResponse
    {
        $response = new HttpResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'handler' => static::class,
            'params' => $params,
        ]));

        return $response;
    }
}
