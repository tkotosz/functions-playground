<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\UuidV4;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Data\GetUserQuery;

class TransformToGetUserQuery
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }
    
    public function __invoke(Request $request): GetUserQuery
    {
        return GetUserQuery::fromUserId(
            UuidV4::fromString($request->attributes->getString('user_id'))
        );
    }
}
