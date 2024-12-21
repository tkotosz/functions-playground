<?php

namespace Tkotosz\Pipeline\Component;

use Tkotosz\Pipeline\Error\RejectPipelineInput;

class ChoosePipeline
{
    private function __construct(){}

    public static function firstThatAcceptsInput(): Choose
    {
        return Choose::firstWithResultThatMatch(
            fn($result) => !$result instanceof RejectPipelineInput
        )->withDefault(RejectPipelineInput::withReason('No Choice Found'));
    }
}
