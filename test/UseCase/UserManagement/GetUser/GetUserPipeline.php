<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser;

use Symfony\Component\Routing\Requirement\Requirement;
use Tkotosz\Pipeline\Component\AcceptRoute;
use Tkotosz\Pipeline\Http\Route;
use Tkotosz\Pipeline\Pipeline;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Component\FetchUserFromDatabase;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Component\HandleGetUserErrors;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Component\TransformToGetUserQuery;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Component\TransformToJsonResponse;

class GetUserPipeline
{
    public function __construct() {}

    public static function create(): Pipeline
    {
        return Pipeline::named('Get User')
            ->pipe(
                AcceptRoute::thatMatch(
                    Route::forPath('/user/id/{user_id}')
                        ->setMethods('GET')
                        ->addRequirements(['user_id' => Requirement::UUID_V4])
                )
            )
            ->pipe(TransformToGetUserQuery::create())
            ->pipe(FetchUserFromDatabase::create())
            ->pipe(TransformToJsonResponse::create())
            ->pipeError(HandleGetUserErrors::create());
    }
}
