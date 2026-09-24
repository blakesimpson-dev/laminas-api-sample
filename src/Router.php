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
            /** @var HandlerInterface $handler */
            $handler = $this->serviceManager->get(
                $routeMatch->getMatchedRouteName(),
            );
        } catch (ServiceNotFoundException $e) {
            $detail = implode(' - ', [$e::class, $e->getMessage()]);
            error_log("Error: \n{$detail}\n");
            return Router::buildNotImplementedResponse();
        }

        /** @var array<string, string> $params */
        $params = $routeMatch->getParams();
        return $handler($params);
    }

    public static function setResponseHeaders(HttpResponse $response): HttpResponse
    {
        $response->getHeaders()->addHeaderLine(
            'Content-Type',
            'application/json',
        );

        return $response;
    }

    /** @param array<string, mixed> $data */
    public static function buildResponse(array $data): HttpResponse
    {
        $response = Router::setResponseHeaders(new HttpResponse());
        $response->setStatusCode(200);
        $response->setContent(json_encode($data, JSON_THROW_ON_ERROR));

        return $response;
    }

    public static function buildBadRequestResponse(): HttpResponse
    {
        $response = Router::setResponseHeaders(new HttpResponse());
        $response->setStatusCode(400);
        $response->setContent(json_encode(['error' => 'Bad Request']));

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
