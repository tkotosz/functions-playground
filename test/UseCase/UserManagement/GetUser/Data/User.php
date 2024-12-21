<?php

namespace Tkotosz\Pipeline\Test\UseCase\UserManagement\GetUser\Data;

use JsonSerializable;
use Symfony\Component\Uid\UuidV4;

class User implements JsonSerializable
{
    public function __construct(
        public readonly UuidV4 $id,
        public readonly string $name
    ) {}

    public static function create(UuidV4 $id, string $name): self
    {
        return new self($id, $name);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name
        ];
    }
}
