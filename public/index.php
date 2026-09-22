<?php

declare(strict_types=1);

require(dirname(__DIR__) . '/vendor/autoload.php');

// use LaminasApiSample\ProfileHandler;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\Literal;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\ServiceManager\ServiceManager;
// use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

// =============================================================================
// Route stack configuration
// =============================================================================

// TODO(Blake):
// Wire new route stack in /config/routes.php
$router = new TreeRouteStack();

// SCOPE: account:profile
$router->addRoute('profile', new Literal('/profile'));

// SCOPE: account:item_filter
$router->addRoute('item-filter-list', new Segment('/item-filter'));
$router->addRoute('item-filter', new Segment('/item-filter/:id'));

// SCOPE: account:characters
$router->addRoute('character-list', new Segment('/character[/:realm]'));
$router->addRoute('character', new Segment('/character[/:realm]/:name'));

// =============================================================================
// Service manager
// =============================================================================

// TODO(Blake):
// Wire new service manager in /config/container.php
$service_manager = new ServiceManager([
    'factories' => [
        // ProfileHandler::class => InvokableFactory::class,
    ],
    'aliases' => [
        // 'profile' => ProfileHandler::class,
    ],
]);

// =============================================================================
// Route matching
// =============================================================================

// TODO(Blake):
// Investigate moving this dispatch loop into a dedicated class...
//
// something like:
// ```
// $dispatcher = new Dispatcher($router, $container);
// $response = $dispatcher->dispatch($request);
// $response->send();
// ```
$request = new HttpRequest();
$route_match = $router->match($request);
$response = new HttpResponse();
$response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

if (!$route_match) {
    $response->setStatusCode(404);
    $response->setContent(json_encode(['error' => 'Not Found']));
    $response->send();
    return;
}

try {
    $handler = $service_manager->get($route_match->getMatchedRouteName());
} catch (ServiceNotFoundException) {
    $response->setStatusCode(501);
    $response->setContent(json_encode(['error' => 'Not Implemented']));
    $response->send();
    return;
}

$response = $handler($route_match->getParams());
$response->send();
