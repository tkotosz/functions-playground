<?php

namespace Tkotosz\Pipeline\Test\Shared\Error;

use Exception;
use Throwable;
use Tkotosz\Pipeline\Error\ErrorWithNamedConstructor;

class DatabaseError extends ErrorWithNamedConstructor
{
    public static function create(string $message = "", int $code = 0, Throwable $previous = null): self
    {
        return new self($message, $code, $previous);
    }

    public static function fromException(Exception $exception): self
    {
        return new self($exception->getMessage(), $exception->getCode(), $exception);
    }
}
