<?php

declare(strict_types=1);

use Laminas\Router\Http\TreeRouteStack;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\Literal;

return new TreeRouteStack()
    ->addRoute(
        "character",
        new Segment("/character[/:realm]/:name", ["realm" => "xbox|sony"]),
    )
    ->addRoute(
        "character-list",
        new Segment("/character[/:realm]", ["realm" => "xbox|sony"]),
    )
    ->addRoute("item-filter", new Segment("/item-filter/:id"))
    ->addRoute("item-filter-list", new Segment("/item-filter"))
    ->addRoute("profile", new Literal("/profile"));
