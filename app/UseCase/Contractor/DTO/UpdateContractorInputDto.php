<?php

namespace App\UseCase\Contractor\DTO;

class UpdateContractorInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $phone,
        public string $email,
        public string $customerId,
        public ?string $communicationCare = null,
        public ?int $createdBy = null,
        public ?int $updatedBy = null,
    ) {}
}
