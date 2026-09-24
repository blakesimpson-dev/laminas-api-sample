<?php

declare(strict_types=1);

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\TreeRouteStack;

const UUID = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

return new TreeRouteStack()
    ->addRoute(
        'item-filter',
        new Segment(
            '/item-filter/:id',
            ['id' => UUID],
            [
                'handlers' => [
                    'GET' => 'item-filter.read',
                    'POST' => 'item-filter.update',
                ],
            ],
        ),
    )
    ->addRoute('item-filter-list', new Literal('/item-filter', [
        'handlers' => [
            'GET' => 'item-filter.read-many',
            'POST' => 'item-filter.create',
        ],
    ]))
    ->addRoute('profile', new Literal('/profile', [
        'handlers' => ['GET' => 'profile.read'],
    ]));
