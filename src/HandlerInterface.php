<?php

declare(strict_types=1);

namespace LaminasApiSample;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;

interface HandlerInterface
{
    /** @param ?array<string, string> $params */
    public function __invoke(?array $params = null): HttpResponse;
}
