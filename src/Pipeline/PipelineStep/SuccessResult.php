<?php

namespace Tkotosz\Pipeline\Pipeline\PipelineStep;

final class SuccessResult extends Result
{
    protected function __construct(mixed $value)
    {
        parent::__construct($value);
    }

    protected static function fromValue(mixed $value): self
    {
        return new self($value);
    }
}
