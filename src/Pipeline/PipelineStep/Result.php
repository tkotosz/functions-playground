<?php

namespace Tkotosz\Pipeline\Pipeline\PipelineStep;

use Error;
use Tkotosz\Pipeline\Pipeline\PipelineStep\SuccessResult;

abstract class Result
{
    protected function __construct(private readonly mixed $value) {}

    public static function success(mixed $value): SuccessResult
    {
        return SuccessResult::fromValue($value);
    }

    public static function error(Error $error): ErrorResult
    {
        return ErrorResult::fromError($error);
    }

    public function unwrap(): mixed
    {
        return $this->value;
    }
}
