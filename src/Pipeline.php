<?php

namespace Tkotosz\Pipeline;

use Tkotosz\Pipeline\Pipeline\PipelineStep;
use Tkotosz\Pipeline\Pipeline\PipelineStep\ErrorResult;
use Tkotosz\Pipeline\Pipeline\PipelineStep\SuccessResult;
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

        $step = ($step instanceof PipelineStep) ? $step : PipelineStep::fromCallable($step);
        $pipeline->steps[] = [SuccessResult::class, $step];

        return $pipeline;
    }

    public function pipeError(callable $step): self
    {
        $pipeline = clone $this;

        $step = ($step instanceof PipelineStep) ? $step : PipelineStep::fromCallable($step);
        $pipeline->steps[] = [ErrorResult::class, $step];

        return $pipeline;
    }

    public function redirectErrorToSuccess(): self
    {
        $pipeline = clone $this;

        $lastStepIndex = count($pipeline->steps) - 1;
        $pipeline->steps[$lastStepIndex] = PipelineStep::fromCallable($pipeline->steps[$lastStepIndex], redirectErrorToSuccess: true);

        return $pipeline;
    }

    public function __invoke(mixed $input): mixed
    {
        return $this->execute($input);
    }

    public function execute(mixed $input): mixed
    {
        $result = ($input instanceof Result) ? $input : Result::success($input);

        foreach ($this->steps as $step) {
            [$acceptsType, $stepHandler] = $step;

            if (!$result instanceof $acceptsType) {
                continue;
            }

            $result = $stepHandler($result);
        }

        return $result->unwrap();
    }
}
