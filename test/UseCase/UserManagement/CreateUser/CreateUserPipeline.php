<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser;

use Tkotosz\Pipeline\Component\AcceptRoute;
use Tkotosz\Pipeline\Http\Route;
use Tkotosz\Pipeline\Pipeline;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component\HandleCreateUserErrors;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component\SaveUserToDatabase;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component\TransformToCreateUserCommand;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component\TransformToJsonResponse;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Component\TransformToUser;

class CreateUserPipeline
{
    public function __construct() {}

    public static function create(): Pipeline
    {
        return Pipeline::named('Create User')
            ->pipe(
                AcceptRoute::thatMatch(
                    Route::forPath('/user')
                        ->setMethods('POST')
                )
            )
            ->pipe(TransformToCreateUserCommand::create())
            ->pipe(TransformToUser::create())
            ->pipe(SaveUserToDatabase::create())
            ->pipe(TransformToJsonResponse::create())
            ->pipeError(HandleCreateUserErrors::create());
    }
}
