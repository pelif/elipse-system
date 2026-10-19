<?php

namespace App\UseCase\ComissionedAgent\DTO;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent;

class ComissionedAgentOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public float $defaultComission,
        public int $paymentDays,
        public ?string $observations = null,
        public bool $active = true,
        public ?int $createdBy = null,
        public ?int $updatedBy = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    public static function fromEntity(?ComissionedAgent $entity): ?self
    {
        if ($entity === null) {
            return null;
        }

        return new self(
            id: $entity->getId(),
            name: $entity->getName(),
            defaultComission: $entity->getDefaultComission(),
            paymentDays: $entity->getPaymentDays(),
            observations: $entity->getObservations(),
            active: $entity->isActive(),
            createdBy: $entity->getCreatedBy(),
            updatedBy: $entity->getUpdatedBy(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
        );
    }
}
