<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Component;

use Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Error\UserNotFoundError;

class HandleGetUserErrors
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function __invoke(Error $error): JsonResponse|Error
    {
        return match(true) {
            $error instanceof UserNotFoundError => new JsonResponse(['error' => $error->getMessage()], Response::HTTP_NOT_FOUND),
            // unexpected error, let the application handle it
            default => $error
        };
    }
}
