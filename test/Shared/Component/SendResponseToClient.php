<?php

namespace Tkotosz\Pipeline\Test\Shared\Component;

use Symfony\Component\HttpFoundation\Response;

class SendResponseToClient
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function __invoke(Response $response): Response
    {
        $response->send();

        return $response;
    }
}
