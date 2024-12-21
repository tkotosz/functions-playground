<?php

namespace Tkotosz\Pipeline\Http\RouteMatcher;

final class SuccessResult extends RouteMatchResult
{
    protected function __construct(
        public readonly array $pathParams = []
    ) {}
}
