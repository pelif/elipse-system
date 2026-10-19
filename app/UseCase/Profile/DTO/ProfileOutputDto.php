<?php

namespace App\UseCase\Profile\DTO;

readonly class ProfileOutputDto
{
    public function __construct(
        public ?string $id,
        public string $name,
        public string $email,
        public string $role,
        public string $roleFormatted
    ) {}
}
