<?php

use Symfony\Component\HttpFoundation\JsonResponse;

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
    //->pipe(SendResponseToClient::create())
;

// TESTING

// testing "framework" :D https://gist.github.com/mathiasverraes/9046427
function test($m,$p){echo"\033[3",$p?'2m✔︎':'1m✘'.register_shutdown_function(function(){die(1);})," It $m\033[0m\n";}

test('Test Create User - Success', (function () use ($application) {
    $request = Request::create('/user', 'POST', content: json_encode(['name' => 'Tibor', 'email' => 'kotosy@gmail.com']));
    $response = $application->execute($request);
    
    return (
        $response instanceof JsonResponse &&
        $response->getStatusCode() === 201 &&
        json_decode($response->getContent(), true)['message'] === 'User Created Successfully' &&
        !empty(json_decode($response->getContent(), true)['user_id'])
    );
})());

test('Test Create User - User Name Error', (function () use ($application) {
    $request = Request::create('/user', 'POST', content: json_encode(['name' => 'Test', 'email' => 'test@test.test']));
    $response = $application->execute($request);
    
    return (
        $response instanceof JsonResponse &&
        $response->getStatusCode() === 400 &&
        json_decode($response->getContent(), true)['error'] === 'User name Test is too short (min length: 5)'
    );
})());

test('Test Create User - Error User Exists', (function () use ($application) {
    $request = Request::create('/user', 'POST', content: json_encode(['name' => 'Test1', 'email' => 'alreadyexists@test.test']));
    $response = $application->execute($request);
    
    return (
        $response instanceof JsonResponse &&
        $response->getStatusCode() === 409 &&
        json_decode($response->getContent(), true)['error'] === 'User with email alreadyexists@test.test already exists'
    );
})());

test('Test Create User - Error DB Connect error', (function () use ($application) {
    $request = Request::create('/user', 'POST', content: json_encode(['name' => 'Test2', 'email' => 'dberror@test.test']));
    $response = $application->execute($request);
    
    return (
        $response instanceof JsonResponse &&
        $response->getStatusCode() === 500 &&
        json_decode($response->getContent(), true)['error'] === 'An unexpected error occured while processing your request'
    );
})());

test('Test Get User - Success', (function () use ($application) {
    $request = Request::create('/user/id/ccd2d3d9-9632-4bc2-b099-40175b54f3f8', 'GET');
    $response = $application->execute($request);
    
    return (
        $response instanceof JsonResponse &&
        $response->getStatusCode() === 200 &&
        json_decode($response->getContent(), true)['name'] === 'Tibor'
    );
})());

test('Test Get User - Error User Does Not Exist', (function () use ($application) {
    $request = Request::create('/user/id/ccd2d3d9-9632-4bc2-b099-40175b54f3f9', 'GET');
    $response = $application->execute($request);
    
    return (
        $response instanceof JsonResponse &&
        $response->getStatusCode() === 404
    );
})());

test('Test Get User - Error DB Connect error', (function () use ($application) {
    $request = Request::create('/user/id/d4594905-a8d2-44f9-a703-b31572a0bc46', 'GET');
    $response = $application->execute($request);
    
    return (
        $response instanceof JsonResponse &&
        $response->getStatusCode() === 500
    );
})());

test('Test 404', (function () use ($application) {
    $request = Request::create('/foo/bar', 'GET');
    $response = $application->execute($request);
    
    return (
        $response instanceof JsonResponse &&
        $response->getStatusCode() === 404
    );
})());

test('pipeline returns success result', (function () use ($application) {
    $simplePipeline = Pipeline::named('simple')
        ->pipe(fn(int $x) => $x * 2)    
        ->pipe(fn(int $x) => $x + 1);
    
    return $simplePipeline->execute(10) === 21;
})());

test('pipeline returns error result', (function () use ($application) {
    $simplePipelineWithError = Pipeline::named('simple')
        ->pipe(fn(int $x) => $x * 2)
        ->pipe(fn($x) => new Error('stop!'))    
        ->pipe(fn(int $x) => $x + 1);

    return $simplePipelineWithError->execute(10) instanceof Error;
})());

test('pipeline pipes error', (function () use ($application) {
    $simplePipelineWithErrorHandled = Pipeline::named('simple')
        ->pipe(fn(int $x) => $x * 2)
        ->pipe(fn($x) => new Error('stop!'))    
        ->pipe(fn(int $x) => $x + 1)
        ->pipeError(fn(Error $error) => 'Hello from error handler');

    return $simplePipelineWithErrorHandled->execute(10) === 'Hello from error handler';
})());

test('pipeline pipes to success after resolving error', (function () use ($application) {
    $simplePipelineWithErrorHandledThenContinue = Pipeline::named('simple')
        ->pipe(fn(int $x) => $x * 2)
        ->pipe(fn($x) => new Error('stop!'))    
        ->pipe(fn(int $x) => $x + 1)
        ->pipeError(fn(Error $error) => 2000)
        ->pipe(fn(int $x) => $x * 2);

    return $simplePipelineWithErrorHandledThenContinue->execute(10) === 4000;
})());

test('pipeline can chain error handlers', (function () use ($application) {
    $simplePipelineErrorAfterError = Pipeline::named('simple')
        ->pipe(fn(int $x) => $x * 2)
        ->pipe(fn($x) => new Error('stop!'))    
        ->pipe(fn(int $x) => $x + 1)
        ->pipeError(fn(Error $error) => $error)
        ->pipeError(fn(Error $error) => new Error('aaa',0,$error))
        ->pipeError(fn(Error $error) => $error->getMessage() . ' ' . $error->getPrevious()?->getMessage());

    return $simplePipelineErrorAfterError->execute(10) === 'aaa stop!';
})());
