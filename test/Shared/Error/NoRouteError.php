<?php

namespace Tkotosz\Pipeline\Test\Shared\Error;

use Symfony\Component\HttpFoundation\Request;
use Tkotosz\Pipeline\Error\ErrorWithNamedConstructor;

class NoRouteError extends ErrorWithNamedConstructor
{
    private function __construct(public readonly Request $request)
    {
        parent::__construct(sprintf('No Route Found For %s', $request->getPathInfo()));
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request);
    }
}
