<?php

declare(strict_types=1);

namespace LaminasApiSample\Http;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;

interface HandlerInterface
{
    /** @param array<string, string> $params */
    public function __invoke(HttpRequest $request, array $params): HttpResponse;
}
