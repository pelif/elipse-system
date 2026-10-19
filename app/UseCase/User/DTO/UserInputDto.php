<?php

namespace App\UseCase\User\DTO;

readonly class UserInputDto
{
    public function __construct(
        public ?string $id = null,
        public string $name,
        public string $email,
        public ?string $password = null,
        public string $role
    ) {}
}
