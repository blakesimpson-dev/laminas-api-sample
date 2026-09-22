<?php

declare(strict_types=1);

namespace LaminasApiSample;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;

abstract class RouteHandler
{
    abstract public function __invoke(array $params): HttpResponse;
}
