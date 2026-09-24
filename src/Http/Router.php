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
    public function dispatch(): HttpResponse
    {
        $request = new HttpRequest();
        $routeMatch = $this->routeStack->match($request);
        if (!$routeMatch) {
            return JsonResponseFactory::notFound();
        }

        try {
            /** @var HandlerInterface $handler */
            $handler = $this->serviceManager->get(
                $routeMatch->getMatchedRouteName(),
            );
        } catch (ServiceNotFoundException $e) {
            $detail = implode(' - ', [$e::class, $e->getMessage()]);
            error_log("Error: \n{$detail}\n");
            return JsonResponseFactory::notImplemented();
        }

        /** @var array<string, string> $params */
        $params = $routeMatch->getParams();
        return $handler($params);
    }
}
