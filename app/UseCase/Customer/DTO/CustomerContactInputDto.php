<?php

namespace App\UseCase\Customer\DTO;

class CustomerContactInputDto
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $typeContact = null,
    ) {}
}
