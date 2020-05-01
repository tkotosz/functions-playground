<?php

class Pipeline {
    private $pipelineSteps = [];

    public function pipe($pipelineStep): self
    {
        $this->pipelineSteps[] = $pipelineStep;

        return $this;
    }

    public function process($value)
    {
        return array_reduce(
            $this->pipelineSteps,
            fn($value, $pipelineStep) => $value->bind($pipelineStep),
            Either::right($value)
        );
    }
}

class Maybe
{
    public static function nothing(): Maybe
    {
        return new self();
    }

    public static function just($value): Maybe
    {
        return new self($value);
    }

    public function isNothing(): bool
    {
        return $this->value === null;
    }

    public function isJust(): bool
    {
        return $this->value !== null;
    }

    public function bind(callable $f): Maybe
    {
        if ($this->isJust()) {
            return $f($this->value);
        }

        return $this;
    }

    public function get()
    {
        return $this->value;
    }

    private function __construct($value = null)
    {
        $this->value = $value;
    }
}

class Either
{
    public static function right($value): Either
    {
        return new self(null, $value);
    }

    public static function left($value): Either
    {
        return new self($value, null);
    }

    public function isRight(): bool
    {
        return $this->right !== null;
    }

    public function isLeft(): bool
    {
        return $this->left !== null;
    }

    public function bind(callable $f): Either
    {
        if ($this->isRight()) {
            return $f($this->right);
        }

        return $this;
    }

    public function either(callable $rightHandler, callable $leftHandler)
    {
        if ($this->isRight()) {
            return $rightHandler($this->right);
        }
         return $leftHandler($this->left);
    }

    public function get()
    {
        return $this->right ?? $this->left;
    }

    private function __construct($left, $right)
    {
        $this->left = $left;
        $this->right = $right;
    }
}
