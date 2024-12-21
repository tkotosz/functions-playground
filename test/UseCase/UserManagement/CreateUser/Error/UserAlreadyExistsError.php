<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Error;

final class UserAlreadyExistsError extends UserCreateError
{
    protected function __construct(
        public readonly string $email
    ) {
        parent::__construct(sprintf('User with email %s already exists', $email));
    }
}
