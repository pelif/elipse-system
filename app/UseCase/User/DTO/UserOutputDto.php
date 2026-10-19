<?php

namespace App\UseCase\User\DTO;

readonly class UserOutputDto
{
    public function __construct(
        public ?string $id,
        public string $name,
        public string $email,
        public string $password,
        public string $role
    ) {}
}
