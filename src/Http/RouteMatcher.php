<?php

namespace Tkotosz\Pipeline\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Throwable;
use Tkotosz\Pipeline\Http\RouteMatcher\RouteMatchResult;

class RouteMatcher
{
    private function __construct(private readonly UrlMatcher $urlMatcher){}

    public static function forRoute(Route $route): self
    {
        $routes = new RouteCollection();
        $routes->add('default', $route);

        return new self(new UrlMatcher($routes, new RequestContext()));
    }

    public function match(Request $request): RouteMatchResult
    {
        try {
            $this->urlMatcher->getContext()->fromRequest($request);
            return RouteMatchResult::success($this->urlMatcher->matchRequest($request));
        } catch (Throwable $e) {
            return RouteMatchResult::failure($e->getMessage());
        }
    }
}
