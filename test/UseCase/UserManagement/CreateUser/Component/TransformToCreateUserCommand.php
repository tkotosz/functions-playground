<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component;

use Symfony\Component\HttpFoundation\Request;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Data\CreateUserCommand;

class TransformToCreateUserCommand
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }
    
    public function __invoke(Request $request): CreateUserCommand
    {
        return CreateUserCommand::create(
            $request->getPayload()->getString('email'),
            $request->getPayload()->getString('name')
        );
    }
}
