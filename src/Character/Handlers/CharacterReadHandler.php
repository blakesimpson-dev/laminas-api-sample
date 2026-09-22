<?php

declare(strict_types=1);

namespace LaminasApiSample\Character\Handlers;

use LaminasApiSample\Handler;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;

final class CharacterReadHandler extends Handler
{
    public function __invoke(array $params): HttpResponse {}
}
