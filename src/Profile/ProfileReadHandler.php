<?php

declare(strict_types=1);

namespace LaminasApiSample\Profile;

use LaminasApiSample\Handler;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;

final class ProfileReadHandler extends Handler
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
