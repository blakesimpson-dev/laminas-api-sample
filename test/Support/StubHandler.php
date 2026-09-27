<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Support;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\Http\Auth\AuthContext;
use LaminasApiSample\Http\HandlerInterface;
use LaminasApiSample\Http\JsonResponseFactory;
use Override;

final class StubHandler implements HandlerInterface
{
    /** @var array<string, string>|null */
    public ?array $receivedParams = null;
    public ?AuthContext $receivedAuth = null;

    /** @param array<string, string> $params */
    #[Override]
    public function __invoke(
        HttpRequest $request,
        array $params,
        AuthContext $auth,
    ): HttpResponse {
        $this->receivedParams = $params;
        $this->receivedAuth = $auth;

        return JsonResponseFactory::ok([]);
    }
}
