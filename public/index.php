<?php

declare(strict_types=1);

require(dirname(__DIR__) . '/vendor/autoload.php');

use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Router;

/** @var TreeRouteStack $route_stack */
$route_stack = require dirname(__DIR__) . '/config/route_stack.php';

/** @var ServiceManager $service_manager */
$service_manager = require dirname(__DIR__) . '/config/service_manager.php';

$response = new Router($route_stack, $service_manager)->dispatch();
$response->send();
