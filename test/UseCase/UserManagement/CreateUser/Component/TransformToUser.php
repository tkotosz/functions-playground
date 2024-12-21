<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component;

use Symfony\Component\Uid\UuidV4;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Data\CreateUserCommand;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Data\User;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Error\UserCreateError;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Error\UserNameTooShortError;

class TransformToUser
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function __invoke(CreateUserCommand $command): User|UserNameTooShortError
    {
        if (strlen($command->name) < 5) {
            return UserCreateError::userNameTooShort($command->name, 5);
        }

        return User::create(
            UuidV4::v4(),
            $command->email,
            $command->name
        );
    }
}
