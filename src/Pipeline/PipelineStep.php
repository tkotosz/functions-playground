<?php

namespace Tkotosz\Pipeline\Pipeline;

use Closure;
use Error;
use Throwable;
use Tkotosz\Pipeline\Pipeline\PipelineStep\Result;

final class PipelineStep
{
    private function __construct(
        private readonly Closure $process
    ) {}

    public static function fromCallable(callable $process): self
    {
        return new self($process(...));
    }

    public function __invoke(Result $input): Result
    {
        try {
            $result = ($this->process)($input->unwrap());
        } catch (Throwable $e) {
            $result = new Error($e->getMessage(), $e->getCode(), $e);
        }

        return match(true) {
            $result instanceof Result => $result,
            $result instanceof Error => Result::error($result),
            default => Result::success($result)
        };
    }
}
