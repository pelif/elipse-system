<?php

namespace App\UseCase\ThirdParty\DTO;

class UpdateThirdPartyInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $alias,
        public string $phone,
        public ?string $phoneAlternative,
        public string $email,
        public ?string $emailAlternative,
        public ?string $indication,
        public ?string $observation,
        public ?string $companyName,
        public string $document,
        public bool $active = true,
        public ?int $createdBy = null,
        public ?int $updatedBy = null,
        public array $serviceIds = [],
    ) {}
}
