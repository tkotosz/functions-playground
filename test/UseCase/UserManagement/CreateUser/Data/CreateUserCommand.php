<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Data;

class CreateUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $name
    ) {}

    public static function create(string $email, string $name): self
    {
        return new self($email, $name);
    }
}
