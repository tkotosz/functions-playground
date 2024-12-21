<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component;

use Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Error\UserAlreadyExistsError;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Error\UserCreateError;

class HandleCreateUserErrors
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function __invoke(Error $error): JsonResponse|Error
    {
        return match(true) {
            $error instanceof UserCreateError => match(true) {
                $error instanceof UserAlreadyExistsError => new JsonResponse(['error' => $error->getMessage()], Response::HTTP_CONFLICT),
                // give 400 for the rest of the error types, but we could control them separately as well
                default => new JsonResponse(['error'=> $error->getMessage()], Response::HTTP_BAD_REQUEST)
            },
            // unexpected infra error, we don't handle this in the main pipeline
            default => $error
        };
    }
}
