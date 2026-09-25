<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Http;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\Http\HandlerInterface;
use LaminasApiSample\Http\JsonResponseFactory;
use Override;

final class MockHandler implements HandlerInterface
{
    /** @var array<string, string>|null */
    public ?array $received = null;

    /** @param array<string, string> $params */
    #[Override]
    public function __invoke(HttpRequest $request, array $params): HttpResponse
    {
        $this->received = $params;

        return JsonResponseFactory::ok([]);
    }
}
