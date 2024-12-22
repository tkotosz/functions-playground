<?php

namespace Tkotosz\Pipeline\Pipeline\PipelineStep;

use Error;

final class ErrorResult extends Result
{
    protected function __construct(mixed $value)
    {
        parent::__construct($value);
    }

    protected static function fromError(Error $error): self
    {
        return new self($error);
    }
}
