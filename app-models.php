<?php

class Request
{
    private array $data;
    
    public static function fromArray(array $data): Request
    {
        return new self($data);
    }

    public function get(string $key): Maybe
    {
        return isset($this->data[$key]) ? Maybe::just($this->data[$key]) : Maybe::nothing();
    }

    private function __construct(array $data)
    {
        $this->data = $data;
    }
}

class Response
{
    private array $data;
    
    public static function fromArray(array $data): Response
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function __construct(array $data)
    {
        $this->data = $data;
    }
}

class CommandSuccess
{
    private string $message;

    public static function fromString(string $message): CommandSuccess
    {
        return new self($message);
    }

    public function toString(): string
    {
        return $this->message;
    }

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

class CommandFailure
{
    private string $message;

    public static function fromString(string $message): CommandFailure
    {
        return new self($message);
    }

    public function toString(): string
    {
        return $this->message;
    }

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

class Command
{
    public const ACTION_CREATE_PRODUCT = 'create_product';

    private string $action;
    private array $parameters;

    public static function from(string $action, array $parameters): Command
    {
        return new self($action, $parameters);
    }

    public function action(): string
    {
        return $this->action;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function __construct(string $action, array $parameters)
    {
        $this->action = $action;
        $this->parameters = $parameters;
    }
}

interface CommandHandler
{
    public function execute(Command $command): Either; // Either<CommandFailure,CommandSuccess>
}
