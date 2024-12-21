<?php

namespace Tkotosz\Pipeline\Http\RouteMatcher;

final class FailureResult extends RouteMatchResult
{
    protected function __construct(
        public readonly string $reason = ''
    ) {}
}
