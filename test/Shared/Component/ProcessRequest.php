<?php

namespace Tkotosz\Pipeline\Test\Shared\Component;

use Closure;
use Error;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessRequest
{
    public function __construct(private Closure $requestProcessor) {}

    public static function with(callable $requestProcessor): self
    {
        return new self($requestProcessor(...));
    }

    public function __invoke(Request $request): Response|Error
    {
        $result = ($this->requestProcessor)($request);

        return match(true) {
            $result instanceof Response => $result->prepare($request),
            $result instanceof Error => $result
        };
    }
}
