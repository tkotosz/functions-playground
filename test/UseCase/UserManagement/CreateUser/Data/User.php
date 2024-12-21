<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\CreateUser\Data;

use Symfony\Component\Uid\UuidV4;

class User
{
    public function __construct(
        public readonly UuidV4 $id,
        public readonly string $email,
        public readonly string $name
    ) {}

    public static function create(UuidV4 $id, string $email, string $name): self
    {
        return new self($id, $email, $name);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'email' => $this->email,
            'name' => $this->name
        ];
    }
}
