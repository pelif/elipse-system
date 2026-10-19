<?php

namespace App\UseCase\Customer\DTO;

use App\Domain\Customer\Entities\ContactEntity;

class CustomerContactOutputDto
{
    public function __construct(
        public ?string $id,
        public ?string $customerId,
        public ?string $name,
        public ?string $phone,
        public ?string $email,
        public ?string $typeContact,
        public ?int $createdBy,
        public ?int $updatedBy,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromEntity(ContactEntity $entity): self
    {
        return new self(
            id: $entity->getId(),
            customerId: $entity->getCustomerId(),
            name: $entity->getName(),
            phone: $entity->getPhone(),
            email: $entity->getEmail(),
            typeContact: $entity->getTypeContact(),
            createdBy: $entity->getCreatedBy(),
            updatedBy: $entity->getUpdatedBy(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
        );
    }
}
