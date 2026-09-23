<?php

declare(strict_types=1);

namespace LaminasApiSample;

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

    public function dispatch(): HttpResponse
    {
        $request = new HttpRequest();
        $routeMatch = $this->routeStack->match($request);

        $response = new HttpResponse();
        $response->getHeaders()->addHeaderLine(
            'Content-Type',
            'application/json',
        );
        if (!$routeMatch) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(['error' => 'Not Found']));
            return $response;
        }

        try {
            /** @var AbstractHandler $handler */
            $handler = $this->serviceManager->get(
                $routeMatch->getMatchedRouteName(),
            );
        } catch (ServiceNotFoundException) {
            $response->setStatusCode(501);
            $response->setContent(json_encode(['error' => 'Not Implemented']));
            return $response;
        } catch (ContainerExceptionInterface) {
            $response->setStatusCode(500);
            $response->setContent(json_encode([
                'error' => 'Internal Server Error',
            ]));
            return $response;
        }

        return $handler($routeMatch->getParams());
    }
}
