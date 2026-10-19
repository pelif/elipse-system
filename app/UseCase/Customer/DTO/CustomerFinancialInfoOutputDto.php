<?php

namespace App\UseCase\Customer\DTO;

use App\Domain\Customer\Entities\FinancialInfoEntity;

class CustomerFinancialInfoOutputDto
{
    public function __construct(
        public ?string $id,
        public ?string $customerId,
        public ?int $deadlinePayment,
        public ?int $antecipation,
        public ?string $antecipationInstructions,
        public ?int $createdBy,
        public ?int $updatedBy,
        public ?string $createdAt,
        public ?string $updatedAt,
        public ?string $emailNf,
        public ?string $financialContactName,
        public ?string $financialContactPhone,
        public ?string $financialContactEmail,
    ) {}

    public static function fromEntity(?FinancialInfoEntity $entity): ?self
    {
        if ($entity === null) {
            return null;
        }

        return new self(
            id: $entity->getId(),
            customerId: $entity->getCustomerId(),
            deadlinePayment: $entity->getDeadlinePayment(),
            antecipation: $entity->getAntecipation(),
            antecipationInstructions: $entity->getAntecipationInstructions(),
            createdBy: $entity->getCreatedBy(),
            updatedBy: $entity->getUpdatedBy(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
            emailNf: $entity->getEmailNf(),
            financialContactName: $entity->getFinancialContactName(),
            financialContactPhone: $entity->getFinancialContactPhone(),
            financialContactEmail: $entity->getFinancialContactEmail(),
        );
    }
}
