<?php

namespace App\UseCase\Resource\DTO;

use App\Domain\Resource\Entities\Resource;

class ResourceOutputDto
{
    public function __construct(
        public string $id,
        public ?string $categoryId,
        public ?string $departmentId,
        public ?string $resourceName,
        public ?string $patrimonialCode,
        public ?string $manufacturer,
        public ?string $model,
        public ?string $serialNumber,
        public ?int $responsibleId,
        public ?string $notes,
        public ?string $documentationLink,
        public ?int $createdBy,
        public ?int $updatedBy,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromEntity(Resource $resource): self
    {
        $id = $resource->getId();
        if ($id === null || $id === '') {
            throw new \LogicException('Resource must have an id to build ResourceOutputDto.');
        }

        return new self(
            id: $id,
            categoryId: $resource->getCategoryId(),
            departmentId: $resource->getDepartmentId(),
            resourceName: $resource->getResourceName(),
            patrimonialCode: $resource->getPatrimonialCode(),
            manufacturer: $resource->getManufacturer(),
            model: $resource->getModel(),
            serialNumber: $resource->getSerialNumber(),
            responsibleId: $resource->getResponsibleId(),
            notes: $resource->getNotes(),
            documentationLink: $resource->getDocumentationLink(),
            createdBy: $resource->getCreatedBy(),
            updatedBy: $resource->getUpdatedBy(),
            createdAt: $resource->getCreatedAt(),
            updatedAt: $resource->getUpdatedAt(),
        );
    }
}
