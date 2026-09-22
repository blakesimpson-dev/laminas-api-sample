<?php

declare(strict_types=1);

require(dirname(__DIR__) . '/vendor/autoload.php');

use Laminas\Router\Http\TreeRouteStack;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\Literal;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;

// =============================================================================
// Route stack configuration
// =============================================================================
$router = new TreeRouteStack();

// SCOPE: account:profile
$router->addRoute('profile', new Literal('/profile'));

// SCOPE: account:item_filter
$router->addRoute('item-filter-list', new Segment('/item-filter'));
$router->addRoute('item-filter', new Segment('/item-filter/:id'));

// SCOPE: account:leagues
$router->addRoute(
    'account-league-list',
    new Segment('/account/leagues[/:realm]'),
);

// SCOPE: account:characters
$router->addRoute('character-list', new Segment('/character[/:realm]'));
$router->addRoute('character', new Segment('/character[/:realm]/:name'));

// SCOPE: account:stashes
$router->addRoute(
    'account-stash-tab-list',
    new Segment('/stash[/:realm]/:league'),
);
$router->addRoute(
    'account-stash-tab',
    new Segment('/stash[/:realm]/:league/:stash_id[/:substash_id]'),
);

// SCOPE: account:league_accounts
$router->addRoute(
    'league-account',
    new Segment('/league-account[/:realm]/:league'),
);

// SCOPE: account:guild:stashes
$router->addRoute(
    'guild-stash-tab-list',
    new Segment('/guild[/:realm]/stash/:league'),
);
$router->addRoute(
    'guild-stash-tab',
    new Segment('/guild[/:realm]/stash/:league/:stash_id[/:substash_id]'),
);

// SCOPE: service:leagues
$router->addRoute('league-list', new Literal('/league'));
$router->addRoute('league', new Segment('/league/:league'));
$router->addRoute('ladder-list', new Segment('/league/:league/ladder'));

// SCOPE: service:pvp_matches
$router->addRoute('pvp-match-list', new Literal('/pvp-match'));
$router->addRoute('pvp-match', new Segment('/pvp-match/:match'));

// SCOPE: service:psapi
$router->addRoute(
    'public-stash-tab-list',
    new Segment('/public-stash-tabs[/:realm]'),
);

// =============================================================================
// Route matching
// =============================================================================

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

$response->setStatusCode(200);
$response->setContent(json_encode([
    'route' => $route_match->getMatchedRouteName(),
    'params' => $route_match->getParams(),
]));
$response->send();
