<?php

namespace Tkotosz\Pipeline;

use Error;
use Tkotosz\Pipeline\Pipeline\PipelineStep;
use Tkotosz\Pipeline\Pipeline\PipelineStep\Result;

class Pipeline
{
    private function __construct(
        private string $name = '',
        private array $steps = []
    ) {
    }

    public static function named(string $name): self
    {
        return new self($name);
    }

    public function pipe(callable $step): self
    {
        $pipeline = clone $this;

        $pipeline->steps[] = match(true) {
            $step instanceof PipelineStep => $step,
            default => PipelineStep::fromSuccessHandler($step)
        };

        return $pipeline;
    }

    public function pipeError(callable $step): self
    {
        $pipeline = clone $this;

        $pipeline->steps[] = match(true) {
            $step instanceof PipelineStep => $step,
            default => PipelineStep::fromErrorHandler($step)
        };

        return $pipeline;
    }

    public function __invoke(mixed $input): mixed
    {
        return $this->execute($input);
    }

    public function execute(mixed $input): mixed
    {
        $result = array_reduce(
            $this->steps,
            fn (Result $result, callable $step) => $step($result),
            $this->wrapInput($input)
        );

        return $result->unwrap();
    }

    private function wrapInput(mixed $input): Result
    {
        return match(true) {
            $input instanceof Result => $input,
            $input instanceof Error => Result::error($input),
            default => Result::success($input)
        };
    }
}
