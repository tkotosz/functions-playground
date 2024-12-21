<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Error;

use Tkotosz\Pipeline\Error\ErrorWithNamedConstructor;

abstract class UserCreateError extends ErrorWithNamedConstructor
{
    public static function userAlreadyExists(string $email): UserAlreadyExistsError
    {
        return new UserAlreadyExistsError($email);
    }

    public static function userNameTooShort(string $userName, int $minLength): UserNameTooShortError
    {
        return new UserNameTooShortError($userName, $minLength);
    }
}
