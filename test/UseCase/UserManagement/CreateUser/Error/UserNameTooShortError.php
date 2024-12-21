<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Error;

final class UserNameTooShortError extends UserCreateError
{
    protected function __construct(
        public readonly string $name,
        public readonly int $minLenght
    ) {
        parent::__construct(sprintf('User name %s is too short (min length: %d)', $name, $minLenght));
    }
}
