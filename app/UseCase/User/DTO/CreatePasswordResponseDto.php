<?php

namespace App\UseCase\User\DTO;

readonly class CreatePasswordResponseDto
{
    public function __construct(
        public bool $success,
        public ?string $error = null,
        public ?UserOutputDto $user = null,
        public ?bool $verified = null,
        public ?bool $hasToken = null
    ) {}
}
