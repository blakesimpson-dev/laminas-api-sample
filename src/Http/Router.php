<?php

declare(strict_types=1);

namespace LaminasApiSample\Http;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerExceptionInterface;

final class Router
{
    public function __construct(
        private TreeRouteStack $routeStack,
        private ServiceManager $serviceManager,
    ) {}

    /** @throws ContainerExceptionInterface */
    public function dispatch(HttpRequest $request): HttpResponse
    {
        $routeMatch = $this->routeStack->match($request);
        if (!$routeMatch) {
            return JsonResponseFactory::notFound();
        }

        $allParams = $routeMatch->getParams();

        /** @var array<string, string> $handlers */
        $handlers = $allParams['handlers'] ?? [];

        /** @var array<string, string> $params */
        $params = array_diff_key($allParams, ['handlers' => true]);

        $serviceName = $handlers[$request->getMethod()] ?? null;
        if (!$serviceName) {
            return JsonResponseFactory::methodNotAllowed(array_keys($handlers));
        }

        try {
            /** @var HandlerInterface $handler */
            $handler = $this->serviceManager->get($serviceName);
        } catch (ServiceNotFoundException $e) {
            $detail = implode(' - ', [$e::class, $e->getMessage()]);
            error_log("Error: \n{$detail}\n");
            return JsonResponseFactory::notImplemented();
        }

        return $handler($request, $params);
    }
}
