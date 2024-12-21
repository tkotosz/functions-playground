<?php

namespace Tkotosz\Pipeline\Component;

use Closure;
use Error;

class Choose
{
    public function __construct(
        private Closure $condition,
        private array $choices = [],
        private mixed $default = null
    ) {
        $this->default = new Error('Non of the available choices matched the defined condition');
    }

    public static function firstWithResultThatMatch(callable $condition)
    {
        return new self($condition(...));
    }

    public function withDefault(mixed $default): self
    {
        $choose = clone $this;
        $choose->default = $default;

        return $choose;
    }

    public function choice(callable $choice): self
    {
        $choose = clone $this;
        $choose->choices[] = $choice;

        return $choose;
    }

    public function __invoke(mixed $input): mixed
    {
        foreach ($this->choices as $choice) {
            $result = $choice($input);

            if (($this->condition)($result)) {
                return $result;
            }
        }

        return $this->default;
    }
}
