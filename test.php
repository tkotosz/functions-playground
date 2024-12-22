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

// Test Create User - Success
$request = Request::create('/user', 'POST', content: json_encode(['name' => 'Tibor', 'email' => 'kotosy@gmail.com']));
$application->execute($request);
echo PHP_EOL;

// Test Create User - User Name Error
$request = Request::create('/user', 'POST', content: json_encode(['name' => 'Test', 'email' => 'test@test.test']));
$application->execute($request);
echo PHP_EOL;

// Test Create User - Error User Exists
$request = Request::create('/user', 'POST', content: json_encode(['name' => 'Test1', 'email' => 'alreadyexists@test.test']));
$application->execute($request);
echo PHP_EOL;

// Test Create User - Error DB Connect error
$request = Request::create('/user', 'POST', content: json_encode(['name' => 'Test2', 'email' => 'dberror@test.test']));
$application->execute($request);
echo PHP_EOL;

// Test Get User - Success
$request = Request::create('/user/id/ccd2d3d9-9632-4bc2-b099-40175b54f3f8', 'GET');
$application->execute($request);
echo PHP_EOL;

// Test Get User - Error User Does Not Exist
$request = Request::create('/user/id/ccd2d3d9-9632-4bc2-b099-40175b54f3f9', 'GET');
$application->execute($request);
echo PHP_EOL;

// Test Get User - Error DB Connect error
$request = Request::create('/user/id/d4594905-a8d2-44f9-a703-b31572a0bc46', 'GET');
$application->execute($request);
echo PHP_EOL;

// Test 404
$request = Request::create('/foo/bar', 'GET');
$application->execute($request);
echo PHP_EOL;


$simplePipeline = Pipeline::named('simple')
    ->pipe(fn(int $x) => $x * 2)    
    ->pipe(fn(int $x) => $x + 1);
    
echo $simplePipeline->execute(10) . PHP_EOL; // 21

$simplePipelineWithError = Pipeline::named('simple')
    ->pipe(fn(int $x) => $x * 2)
    ->pipe(fn($x) => new Error('stop!'))    
    ->pipe(fn(int $x) => $x + 1);

$result = $simplePipelineWithError->execute(10);
var_dump($result instanceof Error) . PHP_EOL; // true

$simplePipelineWithErrorHandled = Pipeline::named('simple')
    ->pipe(fn(int $x) => $x * 2)
    ->pipe(fn($x) => new Error('stop!'))    
    ->pipe(fn(int $x) => $x + 1)
    ->pipeError(fn(Error $error) => 'Hello from error handler');

$result = $simplePipelineWithErrorHandled->execute(10);
var_dump($result) . PHP_EOL; // Hello from error handler

$simplePipelineWithErrorHandledThenContinue = Pipeline::named('simple')
    ->pipe(fn(int $x) => $x * 2)
    ->pipe(fn($x) => new Error('stop!'))    
    ->pipe(fn(int $x) => $x + 1)
    ->pipeError(fn(Error $error) => 2000)
    ->pipe(fn(int $x) => $x * 2);

$result = $simplePipelineWithErrorHandledThenContinue->execute(10);
var_dump($result) . PHP_EOL; // 4000

$simplePipelineErrorAfterError = Pipeline::named('simple')
    ->pipe(fn(int $x) => $x * 2)
    ->pipe(fn($x) => new Error('stop!'))    
    ->pipe(fn(int $x) => $x + 1)
    ->pipeError(fn(Error $error) => $error)
    ->pipeError(fn(Error $error) => new Error('aaa',0,$error))
    ->pipeError(fn(Error $error) => $error->getMessage() . ' ' . $error->getPrevious()?->getMessage());

$result = $simplePipelineErrorAfterError->execute(10);
var_dump($result) . PHP_EOL; // aaa stop!