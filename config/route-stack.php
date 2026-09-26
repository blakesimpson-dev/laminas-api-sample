<?php

declare(strict_types=1);

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\TreeRouteStack;
use LaminasApiSample\Domains\Auth\Scope;
use Ramsey\Uuid\Validator\GenericValidator;

$uuidPattern = str_replace(
    ['\A', '\z'],
    '',
    new GenericValidator()->getPattern(),
);

$itemFilterRoute = new Segment(
    route: '/item-filter/:id',
    constraints: ['id' => $uuidPattern],
    defaults: [
        'scope' => Scope::AccountItemFilter->value,
        'handlers' => [
            'GET' => 'item-filter.read',
            'POST' => 'item-filter.update',
        ],
    ],
);

$itemFilterListRoute = new Literal(route: '/item-filter', defaults: [
    'scope' => Scope::AccountItemFilter->value,
    'handlers' => [
        'GET' => 'item-filter.read-many',
        'POST' => 'item-filter.create',
    ],
]);

$profileRoute = new Literal(route: '/profile', defaults: [
    'scope' => Scope::AccountProfile->value,
    'handlers' => ['GET' => 'profile.read'],
]);

return new TreeRouteStack()
    ->addRoute('item-filter', $itemFilterRoute)
    ->addRoute('item-filter-list', $itemFilterListRoute)
    ->addRoute('profile', $profileRoute);
