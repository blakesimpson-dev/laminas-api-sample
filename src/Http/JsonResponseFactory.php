<?php

declare(strict_types=1);

namespace LaminasApiSample\Http;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;

final class JsonResponseFactory
{
    /** @param array<string, mixed> $data */
    public static function ok(array $data): HttpResponse
    {
        $response = self::withHeaders(new HttpResponse());
        $response->setStatusCode(200);
        $response->setContent(json_encode($data, JSON_THROW_ON_ERROR));

        return $response;
    }

    public static function notFound(): HttpResponse
    {
        return self::error(status: 404, code: 1, message: 'Resource not found');
    }

    /** @param list<string> $allowed $allowed */
    public static function methodNotAllowed(array $allowed): HttpResponse
    {
        $response = self::error(
            status: 405,
            code: 9,
            message: 'Method not allowed.',
        );

        $response->getHeaders()->addHeaderLine('Allow', implode(
            ', ',
            $allowed,
        ));

        return $response;
    }

    public static function unprocessable(string $message): HttpResponse
    {
        return self::error(status: 422, code: 10, message: $message);
    }

    public static function serverError(): HttpResponse
    {
        return self::error(status: 500, code: 4, message: 'Internal error');
    }

    public static function notImplemented(): HttpResponse
    {
        return self::error(status: 501, code: 4, message: 'Internal error');
    }

    private static function error(
        int $status,
        int $code,
        string $message,
    ): HttpResponse {
        $response = self::withHeaders(new HttpResponse());
        $response->setStatusCode($status);
        $response->setContent(json_encode(['error' => [
            'code' => $code,
            'message' => $message,
        ]], JSON_THROW_ON_ERROR));

        return $response;
    }

    private static function withHeaders(HttpResponse $response): HttpResponse
    {
        $response->getHeaders()->addHeaderLine(
            'Content-Type',
            'application/json',
        );

        return $response;
    }
}
