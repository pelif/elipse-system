<?php

namespace App\UseCase\User\DTO;

readonly class PasswordInputDto
{
    public function __construct(
        public int $id,
        public string $password,
    ) {}
}
