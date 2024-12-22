<?php

namespace Tkotosz\Pipeline\Component;

use Symfony\Component\HttpFoundation\Request;
use Tkotosz\Pipeline\Error\RoutingError;
use Tkotosz\Pipeline\Http\Route;
use Tkotosz\Pipeline\Http\RouteMatcher;
use Tkotosz\Pipeline\Http\RouteMatcher\FailureResult;
use Tkotosz\Pipeline\Http\RouteMatcher\SuccessResult;

class AcceptRoute
{
    private function __construct(private readonly Route $route) {}

    public static function thatMatch(Route $route): self
    {
        return new self($route);
    }

    public function __invoke(Request $request): Request|RoutingError
    {
        $result = RouteMatcher::forRoute($this->route)->match($request);

        return match(true) {
            $result instanceof SuccessResult => $request->duplicate(attributes: $result->pathParams),
            $result instanceof FailureResult => RoutingError::routeDoesNotMatch($result->reason)
        };
    }
}
