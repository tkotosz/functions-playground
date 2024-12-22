<?php

namespace Tkotosz\Pipeline\Test\Shared\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tkotosz\Pipeline\Test\Shared\Error\NoRouteError;

class NoRouteHandler
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function __invoke(Request $request): NoRouteError
    {
        return NoRouteError::fromRequest($request);
    }
}
