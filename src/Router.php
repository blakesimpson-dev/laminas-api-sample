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

    /** @throws ContainerExceptionInterface */
    public function dispatch(): HttpResponse
    {
        $request = new HttpRequest();
        $routeMatch = $this->routeStack->match($request);
        if (!$routeMatch) {
            return Router::buildNotFoundResponse();
        }

        try {
            /** @var AbstractHandler $handler */
            $handler = $this->serviceManager->get(
                $routeMatch->getMatchedRouteName(),
            );
        } catch (ServiceNotFoundException $e) {
            $detail = implode(' - ', [$e::class, $e->getMessage()]);
            error_log("Error: \n{$detail}\n");
            return Router::buildNotImplementedResponse();
        }

        return $handler($routeMatch->getParams());
    }

    public static function setResponseHeaders(HttpResponse $response): HttpResponse
    {
        $response->getHeaders()->addHeaderLine(
            'Content-Type',
            'application/json',
        );

        return $response;
    }

    public static function buildResponse(mixed $content): HttpResponse
    {
        $response = Router::setResponseHeaders(new HttpResponse());
        $response->setStatusCode(200);
        $response->setContent($content);

        return $response;
    }

    public static function buildNotFoundResponse(): HttpResponse
    {
        $response = Router::setResponseHeaders(new HttpResponse());
        $response->setStatusCode(404);
        $response->setContent(json_encode(['error' => 'Not Found']));

        return $response;
    }

    public static function buildServerErrorResponse(): HttpResponse
    {
        $response = Router::setResponseHeaders(new HttpResponse());
        $response->setStatusCode(500);
        $response->setContent(json_encode([
            'error' => 'Internal Server Error',
        ]));

        return $response;
    }

    public static function buildNotImplementedResponse(): HttpResponse
    {
        $response = Router::setResponseHeaders(new HttpResponse());
        $response->setStatusCode(501);
        $response->setContent(json_encode(['error' => 'Not Implemented']));

        return $response;
    }
}
