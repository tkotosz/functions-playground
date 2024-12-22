<?php

namespace Tkotosz\Pipeline\Error;

class RoutingError extends ErrorWithNamedConstructor
{
    private function __construct(string $reason)
    {
        parent::__construct($reason);
    }

    public static function routeDoesNotMatch(string $reason): self
    {
        return new self($reason);
    }
}
