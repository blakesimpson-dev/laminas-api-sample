<?php

declare(strict_types=1);

namespace LaminasApiSample;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;

final class Router
{
    public function __construct(
        private TreeRouteStack $route_stack,
        private ServiceManager $service_manager,
    ) {}

    public function dispatch(): HttpResponse
    {
        $request = new HttpRequest();
        $route_match = $this->route_stack->match($request);

        $response = new HttpResponse();
        $response->getHeaders()->addHeaderLine(
            'Content-Type',
            'application/json',
        );
        if (!$route_match) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(['error' => 'Not Found']));
            return $response;
        }

        try {
            $handler = $this->service_manager->get(
                $route_match->getMatchedRouteName(),
            );
        } catch (ServiceNotFoundException) {
            $response->setStatusCode(501);
            $response->setContent(json_encode(['error' => 'Not Implemented']));
            return $response;
        }

        return $handler($route_match->getParams());
    }
}
