<?php

namespace Tkotosz\Pipeline;

use Closure;
use Error;
use Throwable;
use Tkotosz\Pipeline\Error\RejectPipelineInput;

class Pipeline
{
    private function __construct(
        private string $name = '',
        private array $stages = [],
        private ?Closure $errorHandler = null,
        private ?Closure $rejectHandler = null,
        private ?Closure $resultHandler = null,
        private bool $stopOnError = true
    ) {
        $this->errorHandler ??= fn ($x) => $x;
        $this->rejectHandler ??= fn ($x) => $x;
        $this->resultHandler ??= fn ($x) => $x;
    }

    public static function named(string $name): self
    {
        return new self($name);
    }

    public function pipe(callable $stage): self
    {
        $pipeline = clone $this;
        $pipeline->stages[] = $stage;

        return $pipeline;
    }

    public function pipeError(callable $errorHandler): self
    {
        $pipeline = clone $this;
        $pipeline->errorHandler = $errorHandler(...);

        return $pipeline;
    }

    public function pipeRejectToError(): self
    {
        $pipeline = clone $this;
        $pipeline->rejectHandler = &$pipeline->errorHandler;

        return $pipeline;
    }

    public function pipeResult(callable $resultHandler): self
    {
        $pipeline = clone $this;
        $pipeline->resultHandler = $resultHandler(...);

        return $pipeline;
    }

    public function stopOnError(): self
    {
        $pipeline = clone $this;
        $pipeline->stopOnError = true;

        return $pipeline;
    }

    public function continueOnError(): self
    {
        $pipeline = clone $this;
        $pipeline->stopOnError = false;

        return $pipeline;
    }

    public function __invoke(mixed $input): mixed
    {
        return $this->execute($input);
    }

    public function execute(mixed $input): mixed
    {
        $result = $input;

        foreach ($this->stages as $stage) {
            try {
                $result = $stage($result);
            } catch (Throwable $e) {
                $result = new Error($e->getMessage(), $e->getCode(), $e);
            }
            

            if ($result instanceof Error) {
                $result = $this->handleError($result);

                if ($this->stopOnError) {
                    break;
                }
            }
        }

        return ($this->resultHandler)($result);
    }

    private function handleError(Error $error): mixed
    {
        return match(true) {
            ($error instanceof RejectPipelineInput) => ($this->rejectHandler)($error),
            default => ($this->errorHandler)($error)
        };
    }
}
