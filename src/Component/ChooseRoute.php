<?php

namespace Tkotosz\Pipeline\Component;

use Tkotosz\Pipeline\Error\RoutingError;

class ChooseRoute
{
    private function __construct(){}
    
    public static function firstThatAcceptsRequest(): Choose
    {
        return Choose::firstWithResultThatMatch(fn($result) => !$result instanceof RoutingError);
    }
}
