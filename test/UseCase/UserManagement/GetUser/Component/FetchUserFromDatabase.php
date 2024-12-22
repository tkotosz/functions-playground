<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Component;

use Exception;
use Symfony\Component\Uid\UuidV4;
use Tkotosz\Pipeline\Test\Shared\Error\DatabaseError;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Data\GetUserQuery;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Data\User;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Error\UserNotFoundError;

class FetchUserFromDatabase
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }
    
    public function __invoke(GetUserQuery $query): User|UserNotFoundError|DatabaseError
    {
        try {
            $result = $this->fetchFromDatabase($query->userId->toString());
        } catch(Exception $exception) {
            return DatabaseError::fromException($exception);
        }

        if (empty($result)) {
            return UserNotFoundError::fromUserId($query->userId);
        }

        return User::create(
            UuidV4::fromString($result['user_id']),
            $result['name']
        );
    }
    
    /** @throws Exception */
    private function fetchFromDatabase(string $userId): array
    {
        if ($userId === 'd4594905-a8d2-44f9-a703-b31572a0bc46') {
            throw new Exception('DB connect error');
        }

        if ($userId !== 'ccd2d3d9-9632-4bc2-b099-40175b54f3f8') {
            return [];
        }

        return [
            'user_id' => $userId,
            'name' => 'Tibor'
        ];
    }
}
