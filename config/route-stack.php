<?php

declare(strict_types=1);

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\TreeRouteStack;

const UUID = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

return new TreeRouteStack()
    ->addRoute('item-filter', new Segment('/item-filter/:id', ['id' => UUID]))
    ->addRoute('item-filter-list', new Literal('/item-filter'))
    ->addRoute('profile', new Literal('/profile'));
