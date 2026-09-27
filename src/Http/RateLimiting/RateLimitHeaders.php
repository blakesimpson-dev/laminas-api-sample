<?php

declare(strict_types=1);

namespace LaminasApiSample\Http\RateLimiting;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;

final readonly class RateLimitHeaders
{
    public static function apply(
        HttpResponse $response,
        RateLimitResult $result,
    ): HttpResponse {
        $rule = ucfirst($result->rule->name);
        $headers = $response->getHeaders();
        $headers->addHeaderLine('X-Rate-Limit-Policy', $result->policy);
        $headers->addHeaderLine('X-Rate-Limit-Rules', $result->rule->name);
        $headers->addHeaderLine(
            "X-Rate-Limit-{$rule}",
            $result->rule->header(),
        );
        $headers->addHeaderLine(
            "X-Rate-Limit-{$rule}-State",
            $result->stateHeader(),
        );

        if ($result->isLimited()) {
            $headers->addHeaderLine(
                'Retry-After',
                (string) $result->restrictedFor,
            );
        }

        return $response;
    }
}
