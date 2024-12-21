<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Data;

use Symfony\Component\Uid\UuidV4;

class GetUserQuery
{
    private function __construct(
        public readonly UuidV4 $userId
    ) {}

    public static function fromUserId(UuidV4 $userId): self
    {
        return new self($userId);
    }
}
