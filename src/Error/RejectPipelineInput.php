<?php

namespace Tkotosz\Pipeline\Error;

class RejectPipelineInput extends ErrorWithNamedConstructor
{
    public static function withReason(string $reason): self
    {
        return new self($reason);
    }
}
