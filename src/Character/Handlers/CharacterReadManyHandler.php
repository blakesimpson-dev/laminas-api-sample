<?php

declare(strict_types=1);

namespace LaminasApiSample\Character\Handlers;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\AbstractHandler;
use Override;

final class CharacterReadManyHandler extends AbstractHandler
{
    /** @param ?array<array-key, mixed> $params */
    #[Override]
    public function __invoke(?array $params = null): HttpResponse
    {
        $response = new HttpResponse();
        $response->setStatusCode(200);
        $response->setContent(
            json_encode([
                "handler" => static::class,
                "params" => $params,
            ]),
        );

        return $response;
    }
}
