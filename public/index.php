<?php

declare(strict_types=1);

use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Router;
use Psr\Container\ContainerExceptionInterface;

try {
    require dirname(__DIR__) . '/bootstrap.php';
    handle_request();
} catch (Throwable $e) {
    $detail = implode(' - ', [$e::class, $e->getMessage()]);
    error_log($detail);
    Router::buildServerErrorResponse()->send();
}

/** @throws ContainerExceptionInterface */
function handle_request(): void
{
    /** @var TreeRouteStack $routeStack */
    $routeStack = require dirname(__DIR__) . '/config/route-stack.php';
    /** @var ServiceManager $serviceManager */
    $serviceManager = require dirname(__DIR__) . '/config/service-manager.php';

    $router = new Router($routeStack, $serviceManager);
    $response = $router->dispatch();

    $response->send();
}
