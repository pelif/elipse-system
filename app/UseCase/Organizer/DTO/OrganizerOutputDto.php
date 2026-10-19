<?php

namespace App\UseCase\Organizer\DTO;

use App\Domain\Organizer\Entities\Organizer;

class OrganizerOutputDto
{
    public function __construct(
        public string $id,
        public ?string $customerId,
        public ?string $customerName,
        public string $name,
        public string $phone,
        public string $email,
        public ?string $communicationCare,
        public ?string $observations,
        public ?string $typeContact,
        public ?int $createdBy,
        public ?int $updatedBy,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromEntity(Organizer $organizer): self
    {
        $id = $organizer->getId();

        if ($id === null || $id === '')
            throw new \LogicException('Organizer must have an id to build OrganizerOutputDto.');

        return new self(
            id: $id,
            customerId: $organizer->getCustomerId(),
            customerName: $organizer->getCustomerName(),
            name: $organizer->getName(),
            phone: $organizer->getPhone(),
            email: $organizer->getEmail(),
            communicationCare: $organizer->getCommunicationCare(),
            observations: $organizer->getObservations(),
            typeContact: $organizer->getTypeContact(),
            createdBy: $organizer->getCreatedBy(),
            updatedBy: $organizer->getUpdatedBy(),
            createdAt: $organizer->getCreatedAt(),
            updatedAt: $organizer->getUpdatedAt(),
        );
    }
}
