<?php

declare(strict_types=1);

namespace LaminasApiSample\Http;

use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\MultipleHeaderInterface;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;

/**
 * Sends a response without using Laminas ->send() PHP forces 401 whenever a
 * WWW-Authenticate header is sent... and a status header() blocks using
 * http_response_code()
 *
 * So headers go first, then the real status, then the body
 */

final class ResponseEmitter
{
    public static function emit(HttpResponse $response): void
    {
        /** @var HeaderInterface $header */
        foreach ($response->getHeaders() as $header) {
            header(
                $header->toString(),
                !$header instanceof MultipleHeaderInterface,
            );
        }

        http_response_code($response->getStatusCode());
        echo $response->getBody();
    }
}
