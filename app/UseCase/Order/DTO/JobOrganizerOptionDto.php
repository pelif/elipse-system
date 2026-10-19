<?php

namespace App\UseCase\Order\DTO;

class JobOrganizerOptionDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $phone,
        public string $email,
    ) {}
}
