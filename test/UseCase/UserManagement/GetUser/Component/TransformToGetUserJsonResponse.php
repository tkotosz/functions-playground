<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Component;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Data\User;

class TransformToGetUserJsonResponse
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }
    
    public function __invoke(User $user): JsonResponse
    {
        return new JsonResponse($user, Response::HTTP_OK);
    }
}
