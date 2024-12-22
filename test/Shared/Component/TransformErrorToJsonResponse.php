<?php

namespace Tkotosz\Pipeline\Test\Shared\Component;

use Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tkotosz\Pipeline\Test\Shared\Error\NoRouteError;

class TransformErrorToJsonResponse
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function __invoke(Error $error): JsonResponse
    {
        var_dump($error::class);
        return match(true) {
            $error instanceof NoRouteError => new JsonResponse(['error' => 'Not Found'], Response::HTTP_NOT_FOUND),
            default => new JsonResponse(['error'=> 'An unexpected error occured while processing your request'], Response::HTTP_INTERNAL_SERVER_ERROR)
        };
    }
}
