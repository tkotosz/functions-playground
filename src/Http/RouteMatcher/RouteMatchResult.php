<?php

namespace Tkotosz\Pipeline\Http\RouteMatcher;

abstract class RouteMatchResult
{
    public static function success(array $pathParams = []): SuccessResult
    {
        return new SuccessResult($pathParams);
    }

    public static function failure(string $reason): FailureResult
    {
        return new FailureResult($reason);
    }
}
