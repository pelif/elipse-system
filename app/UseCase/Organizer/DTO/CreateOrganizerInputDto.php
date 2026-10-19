<?php

namespace App\UseCase\Organizer\DTO;

class CreateOrganizerInputDto
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
        public string $customerId,
        public ?string $communicationCare = null,
        public ?int $createdBy = null,
        public ?int $updatedBy = null,
    ) {}
}
