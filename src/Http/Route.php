<?php

namespace Tkotosz\Pipeline\Http;

use Symfony\Component\Routing\Route as SymfonyRoute;

final class Route extends SymfonyRoute
{
    public static function forPath(string $path): self
    {
        return new self($path);
    }
}
