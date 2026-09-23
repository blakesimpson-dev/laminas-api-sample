<?php

declare(strict_types=1);

namespace LaminasApiSample;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;

abstract class AbstractHandler
{
    /** @param array<array-key, mixed> $params */
    abstract public function __invoke(array $params): HttpResponse;
}
