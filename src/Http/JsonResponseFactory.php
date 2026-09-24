<?php

declare(strict_types=1);

namespace LaminasApiSample\Http;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;

final class JsonResponseFactory
{
    public static function setHeaders(HttpResponse $response): HttpResponse
    {
        $response->getHeaders()->addHeaderLine(
            'Content-Type',
            'application/json',
        );

        return $response;
    }

    /** @param array<string, mixed> $data */
    public static function ok(array $data): HttpResponse
    {
        $response = JsonResponseFactory::setHeaders(new HttpResponse());
        $response->setStatusCode(200);
        $response->setContent(json_encode($data, JSON_THROW_ON_ERROR));

        return $response;
    }

    public static function badRequest(): HttpResponse
    {
        $response = JsonResponseFactory::setHeaders(new HttpResponse());
        $response->setStatusCode(400);
        $response->setContent(json_encode(['error' => 'Bad Request.']));

        return $response;
    }

    public static function notFound(): HttpResponse
    {
        $response = JsonResponseFactory::setHeaders(new HttpResponse());
        $response->setStatusCode(404);
        $response->setContent(json_encode(['error' => 'Not Found.']));

        return $response;
    }

    public static function serverError(): HttpResponse
    {
        $response = JsonResponseFactory::setHeaders(new HttpResponse());
        $response->setStatusCode(500);
        $response->setContent(json_encode([
            'error' => 'Internal Server Error.',
        ]));

        return $response;
    }

    public static function notImplemented(): HttpResponse
    {
        $response = JsonResponseFactory::setHeaders(new HttpResponse());
        $response->setStatusCode(501);
        $response->setContent(json_encode(['error' => 'Not Implemented.']));

        return $response;
    }
}
