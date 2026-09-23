<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use LaminasApiSample\AbstractHandler;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;

final class ProfileReadHandler extends AbstractHandler
{
    public function __invoke(array $params): HttpResponse
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
