<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Error;

use Symfony\Component\Uid\UuidV4;
use Tkotosz\Pipeline\Error\ErrorWithNamedConstructor;

class UserNotFoundError extends ErrorWithNamedConstructor
{
    private function __construct(
        public readonly UuidV4 $userId
    ) {
        parent::__construct(sprintf('User with ID %s not found', $userId->toString()));
    }

    public static function fromUserId(UuidV4 $userId): self
    {
        return new self($userId);
    }
}
