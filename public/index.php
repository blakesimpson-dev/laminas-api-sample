<?php

declare(strict_types=1);

declare(strict_types=1);

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Http\Auth\AuthenticatorInterface;
use LaminasApiSample\Http\JsonResponseFactory;
use LaminasApiSample\Http\RateLimiting\RateLimiter;
use LaminasApiSample\Http\ResponseEmitter;
use LaminasApiSample\Http\Router;

(static function (): void {
    try {
        require dirname(__DIR__) . '/bootstrap.php';

        /** @var TreeRouteStack $routeStack */
        $routeStack = require dirname(__DIR__) . '/config/route-stack.php';
        /** @var ServiceManager $serviceManager */
        $serviceManager = require
            dirname(__DIR__) . '/config/service-manager.php';
        /** @var AuthenticatorInterface $authenticator */
        $authenticator = $serviceManager->get(AuthenticatorInterface::class);
        /** @var RateLimiter $rateLimiter */
        $rateLimiter = $serviceManager->get(RateLimiter::class);

        $router = new Router(
            $routeStack,
            $serviceManager,
            $authenticator,
            $rateLimiter,
        );

        ResponseEmitter::emit($router->dispatch(new HttpRequest()));
    } catch (Throwable $exception) {
        error_log($exception::class . ' - ' . $exception->getMessage());
        ResponseEmitter::emit(JsonResponseFactory::serverError());
    }
})();
