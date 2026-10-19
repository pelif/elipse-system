<?php

namespace App\UseCase\User\DTO;

class PasswordResponseDto
{
    public function __construct(
        public bool $success,
        public ?string $error = null,
        public ?UserOutputDto $user = null,
        public ?bool $verified = false,
        public ?bool $hasToken = false
    ) {}
}
