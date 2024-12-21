<?php

require 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Tkotosz\Pipeline\Component\ChoosePipeline;
use Tkotosz\Pipeline\Pipeline;
use Tkotosz\Pipeline\Test\Shared\Component\HandleApplicationErrors;
use Tkotosz\Pipeline\Test\Shared\Component\LogApplicationErrors;
use Tkotosz\Pipeline\Test\Shared\Component\ProcessRequest;
use Tkotosz\Pipeline\Test\Shared\Component\SendResponseToClient;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\CreateUserPipeline;
use Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\GetUserPipeline;

$application = Pipeline::named('MyApp')
    ->pipe(ProcessRequest::with(
        ChoosePipeline::firstThatAcceptsInput()
            ->choice(GetUserPipeline::create())
            ->choice(CreateUserPipeline::create())
    ))
    ->pipeError(
        Pipeline::named('Request Processor Error Handling')
            ->continueOnError()
            ->pipe(LogApplicationErrors::create())
            ->pipe(HandleApplicationErrors::create())
    )
    ->pipeRejectToError()
    ->pipeResult(SendResponseToClient::create());
;


// Test Create User
$request = Request::create('/user', 'POST', content: json_encode(['name' => 'Tibor', 'email' => 'kotosy@gmail.com']));
$application->execute($request);
echo PHP_EOL;

// Test Get User
$request = Request::create('/user/id/ccd2d3d9-9632-4bc2-b099-40175b54f3f8', 'GET');
$application->execute($request);
echo PHP_EOL;

// Test 404
$request = Request::create('/foo/bar', 'GET');
$application->execute($request);
echo PHP_EOL;
