<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\Http\Auth\AuthContext;
use LaminasApiSample\Http\HandlerInterface;
use LaminasApiSample\Http\JsonResponseFactory;
use Override;

final class ProfileReadHandler implements HandlerInterface
{
    public function __construct(
        private readonly ProfileAdapter $adapter,
    ) {}

    /** @param array<string, string> $params */
    #[Override]
    public function __invoke(
        HttpRequest $request,
        array $params,
        AuthContext $auth,
    ): HttpResponse {
        $profile = $this->adapter->mapResponse($auth->profile);
        return JsonResponseFactory::ok($profile);
    }
}
