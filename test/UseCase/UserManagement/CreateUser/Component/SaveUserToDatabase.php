<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component;

use Exception;
use Tkotosz\Pipeline\Test\Shared\Error\DatabaseError;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Data\User;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Error\UserCreateError;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Error\UserAlreadyExistsError;

class SaveUserToDatabase
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function __invoke(User $user): User|UserAlreadyExistsError|DatabaseError
    {
        try {
            return $this->saveToDatabase($user);
        } catch (Exception $exception) {
            if ($exception->getMessage() === 'Integrity contraint violation') {
                return UserCreateError::userAlreadyExists($user->email);
            }

            return DatabaseError::fromException($exception);
        }
    }

    /** @throws Exception */
    private function saveToDatabase(User $user): User
    {
        if (rand(0,1) === 1) {
            throw new Exception('DB connect error');
        }

        if (rand(0,1) === 1) {
            throw new Exception('Integrity contraint violation');
        }

        return $user;
    }
}
