<?php

declare(strict_types=1);

namespace LaminasApiSample;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;

final class ProfileHandler extends RouteHandler
{
    public function __invoke(array $params): HttpResponse
    {
        $response = new HttpResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'handler' => 'ProfileHandler',
            'params' => $params,
        ]));

        return $response;
    }
}
