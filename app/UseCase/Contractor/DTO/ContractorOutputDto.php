<?php

namespace App\UseCase\Contractor\DTO;

use App\Domain\Contractor\Entities\Contractor;

class ContractorOutputDto
{
    public function __construct(
        public string $id,
        public ?string $customerId,
        public ?string $customerName,
        public string $name,
        public string $phone,
        public string $email,
        public ?string $communicationCare,
        public ?string $typeContact,
        public ?int $createdBy,
        public ?int $updatedBy,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromEntity(Contractor $contractor): self
    {
        $id = $contractor->getId();

        if ($id === null || $id === '')
            throw new \LogicException('Contractor must have an id to build ContractorOutputDto.');

        return new self(
            id: $id,
            customerId: $contractor->getCustomerId(),
            customerName: $contractor->getCustomerName(),
            name: $contractor->getName(),
            phone: $contractor->getPhone(),
            email: $contractor->getEmail(),
            communicationCare: $contractor->getCommunicationCare(),
            typeContact: $contractor->getTypeContact(),
            createdBy: $contractor->getCreatedBy(),
            updatedBy: $contractor->getUpdatedBy(),
            createdAt: $contractor->getCreatedAt(),
            updatedAt: $contractor->getUpdatedAt(),
        );
    }
}
