<?php

namespace App\UseCase\User\DTO;

use App\Domain\User\Entities\User;

class LoginResponseDto
{
    public function __construct(
        public bool $success,
        public ?User $user = null,
        public ?string $error = null
    ) {}
}
