<?php

require 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Tkotosz\Pipeline\Component\ChooseRoute;
use Tkotosz\Pipeline\Pipeline;
use Tkotosz\Pipeline\Test\Shared\Component\LogApplicationErrors;
use Tkotosz\Pipeline\Test\Shared\Component\NoRouteHandler;
use Tkotosz\Pipeline\Test\Shared\Component\ProcessRequest;
use Tkotosz\Pipeline\Test\Shared\Component\SendResponseToClient;
use Tkotosz\Pipeline\Test\Shared\Component\TransformErrorToJsonResponse;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\CreateUserPipeline;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\GetUserPipeline;

$application = Pipeline::named('MyApp')
    ->pipe(ProcessRequest::with(
        ChooseRoute::firstThatAcceptsRequest()
            ->choice(GetUserPipeline::create())
            ->choice(CreateUserPipeline::create())
            ->otherwise(NoRouteHandler::create())
    ))
    ->pipeError(LogApplicationErrors::create())
    ->pipeError(TransformErrorToJsonResponse::create())
    ->pipe(SendResponseToClient::create());

$application->execute(Request::createFromGlobals());

        
