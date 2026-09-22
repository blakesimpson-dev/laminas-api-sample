<?php

declare(strict_types=1);

require(dirname(__DIR__) . '/vendor/autoload.php');

use LaminasApiSample\ProfileHandler;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\Literal;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

// =============================================================================
// Route stack configuration
// =============================================================================

$route_stack = new TreeRouteStack();

// SCOPE: account:profile
$route_stack->addRoute('profile', new Literal('/profile'));

// SCOPE: account:item_filter
$route_stack->addRoute('item-filter-list', new Segment('/item-filter'));
$route_stack->addRoute('item-filter', new Segment('/item-filter/:id'));

// SCOPE: account:leagues
$route_stack->addRoute(
    'account-league-list',
    new Segment('/account/leagues[/:realm]'),
);

// SCOPE: account:characters
$route_stack->addRoute('character-list', new Segment('/character[/:realm]'));
$route_stack->addRoute('character', new Segment('/character[/:realm]/:name'));

// SCOPE: account:stashes
$route_stack->addRoute(
    'account-stash-tab-list',
    new Segment('/stash[/:realm]/:league'),
);
$route_stack->addRoute(
    'account-stash-tab',
    new Segment('/stash[/:realm]/:league/:stash_id[/:substash_id]'),
);

// SCOPE: account:league_accounts
$route_stack->addRoute(
    'league-account',
    new Segment('/league-account[/:realm]/:league'),
);

// SCOPE: account:guild:stashes
$route_stack->addRoute(
    'guild-stash-tab-list',
    new Segment('/guild[/:realm]/stash/:league'),
);
$route_stack->addRoute(
    'guild-stash-tab',
    new Segment('/guild[/:realm]/stash/:league/:stash_id[/:substash_id]'),
);

// SCOPE: service:leagues
$route_stack->addRoute('league-list', new Literal('/league'));
$route_stack->addRoute('league', new Segment('/league/:league'));
$route_stack->addRoute('ladder-list', new Segment('/league/:league/ladder'));

// SCOPE: service:pvp_matches
$route_stack->addRoute('pvp-match-list', new Literal('/pvp-match'));
$route_stack->addRoute('pvp-match', new Segment('/pvp-match/:match'));

// SCOPE: service:psapi
$route_stack->addRoute(
    'public-stash-tab-list',
    new Segment('/public-stash-tabs[/:realm]'),
);

// =============================================================================
// Service manager
// =============================================================================

$service_manager = new ServiceManager([
    'factories' => [
        ProfileHandler::class => InvokableFactory::class,
    ],
    'aliases' => [
        'profile' => ProfileHandler::class,
    ],
]);

// =============================================================================
// Route matching
// =============================================================================

$request = new HttpRequest();
$route_match = $route_stack->match($request);
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
