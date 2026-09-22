<?php

declare(strict_types=1);

namespace LaminasApiSample\ItemFilter\Handlers;

use LaminasApiSample\Handler;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;

final class ItemFilterReadManyHandler extends Handler
{
    public function __invoke(array $params): HttpResponse {}
}
