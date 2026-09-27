<?php

declare(strict_types=1);

use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\MultipleHeaderInterface;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Http\Auth\AuthenticatorInterface;
use LaminasApiSample\Http\JsonResponseFactory;
use LaminasApiSample\Http\Router;
use Psr\Container\ContainerExceptionInterface;

try {
    require dirname(__DIR__) . '/bootstrap.php';
    handle_request();
} catch (Throwable $e) {
    $detail = implode(' - ', [$e::class, $e->getMessage()]);
    error_log($detail);
    send_response(JsonResponseFactory::serverError());
}

/** @throws ContainerExceptionInterface */
function handle_request(): void
{
    /** @var TreeRouteStack $routeStack */
    $routeStack = require dirname(__DIR__) . '/config/route-stack.php';
    /** @var ServiceManager $serviceManager */
    $serviceManager = require dirname(__DIR__) . '/config/service-manager.php';
    /** @var AuthenticatorInterface $authenticator */
    $authenticator = $serviceManager->get(AuthenticatorInterface::class);

    $router = new Router($routeStack, $serviceManager, $authenticator);
    $response = $router->dispatch(new HttpRequest());

    send_response($response);
}

/**
 * Note: PHP forces a 401 whenever a WWW-Authenticate header is sent...
 * ... so $response->send(); is dishonest
 *
 * Here we reassert the status before sending the response content -
 * otherwise 401 is returned for 'insufficient scope' instead of 403
 */
function send_response(HttpResponse $response): void
{
    /** @var HeaderInterface $header */
    foreach ($response->getHeaders() as $header) {
        header(
            $header->toString(),
            !$header instanceof MultipleHeaderInterface,
        );
    }

    http_response_code($response->getStatusCode());
    echo $response->getBody();
}
