<?php

declare(strict_types=1);

require dirname(__DIR__) . "/vendor/autoload.php";

use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Router;

/** @var TreeRouteStack $routeStack */
$routeStack = require dirname(__DIR__) . "/config/route-stack.php";

/** @var ServiceManager $serviceManager */
$serviceManager = require dirname(__DIR__) . "/config/service-manager.php";

$response = new Router($routeStack, $serviceManager)->dispatch();
$response->send();
