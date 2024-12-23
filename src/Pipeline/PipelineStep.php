<?php

namespace Tkotosz\Pipeline\Pipeline;

use Tkotosz\Pipeline\Pipeline\PipelineStep\PipelineStepHandler;
use Tkotosz\Pipeline\Pipeline\PipelineStep\Result;
use Tkotosz\Pipeline\Pipeline\PipelineStep\SuccessResult;
use Tkotosz\Pipeline\Pipeline\PipelineStep\ErrorResult;

final class PipelineStep
{
    public function __construct(
        private PipelineStepHandler $successHandler,
        private PipelineStepHandler $errorHandler
    ) {}

    public static function fromHandlers(callable $successHandler, callable $errorHandler): self
    {
        $successHandler = ($successHandler instanceof PipelineStep) ? $successHandler->successHandler : PipelineStepHandler::fromCallable($successHandler);
        $errorHandler = ($errorHandler instanceof PipelineStep) ? $errorHandler->errorHandler : PipelineStepHandler::fromCallable($errorHandler);

        return new self($successHandler, $errorHandler);
    }

    public static function passthrough(): self
    {
        return self::fromHandlers(PipelineStepHandler::passthrough(), PipelineStepHandler::passthrough());
    }

    public static function create(): self
    {
        return self::passthrough();
    }

    public static function fromSuccessHandler(callable $successHandler): self
    {
        return self::fromHandlers($successHandler, PipelineStepHandler::passthrough());
    }

    public static function fromErrorHandler(callable $errorHandler): self
    {
        return self::fromHandlers(PipelineStepHandler::passthrough(), $errorHandler);
    }

    public function withSuccessHandler(callable $successHandler): self
    {
        return self::fromHandlers($this->errorHandler, $successHandler);
    }

    public function withErrorHandler(callable $errorHandler): self
    {
        return self::fromHandlers($errorHandler, $this->successHandler);
    }

    public function __invoke(Result $input): Result
    {
        return match(true) {
            $input instanceof SuccessResult => ($this->successHandler)($input),
            $input instanceof ErrorResult => ($this->errorHandler)($input)
        };
    }
}
