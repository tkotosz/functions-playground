<?php

namespace Tkotosz\Pipeline\Pipeline\PipelineStep;

use Closure;
use Error;
use Throwable;

class PipelineStepHandler
{
    public function __construct(
        private Closure $process
    ) {}

    public static function fromClosure(Closure $process): self
    {
        return new self($process);
    }

    public static function fromCallable(callable $process): self
    {
        $process = ($process instanceof PipelineStepHandler) ? $process->process : $process;
        
        return self::fromClosure(($process instanceof Closure) ? $process : Closure::fromCallable($process));
    }

    public static function passthrough(): self
    {
        return self::fromClosure(fn($x) => $x);
    }

    public function __invoke(Result $input): Result
    {
        try {
            $result = ($this->process)($input->unwrap());
        } catch (Throwable $error) {
            $result = ($error instanceof Error) ? $error : new Error($error->getMessage(), $error->getCode(), $error);
        }

        return match(true) {
            $result instanceof Result => $result,
            $result instanceof Error => Result::error($result),
            default => Result::success($result)
        };
    }
}
