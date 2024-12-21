<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Data\User;

class TransformToJsonResponse
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }
    
    public function __invoke(User $user): JsonResponse
    {
        return new JsonResponse([
            'message' => 'User Created Successfully',
            'user_id' => $user->id->toString()
        ], Response::HTTP_CREATED);
    }
}
