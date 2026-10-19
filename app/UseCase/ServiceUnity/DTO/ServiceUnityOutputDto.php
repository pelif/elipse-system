<?php

namespace App\UseCase\ServiceUnity\DTO;

use App\Domain\ServiceUnity\Entities\ServiceUnity;

class ServiceUnityOutputDto
{
    public function __construct(
        public string $id,
        public string $unity,
        public int $createdBy,
        public ?int $updatedBy,
        public string $createdAt,
        public ?string $updatedAt
    ) {}

    public static function fromEntity(ServiceUnity $entity): self
    {
        if (empty($entity->getId()))
            throw new \LogicException('Service Unity must have an id to build ServiceUnityOutputDto object');

        return new self(
            id: $entity->getId(),
            unity: $entity->getUnity(),
            createdBy: $entity->getCreator(),
            updatedBy: $entity->getEditor(),
            createdAt: $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            updatedAt: $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        );
    }
}
