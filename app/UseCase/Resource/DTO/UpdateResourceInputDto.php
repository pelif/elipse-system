<?php

namespace App\UseCase\Resource\DTO;

class UpdateResourceInputDto
{
    public function __construct(
        public string $id,
        public string $categoryId,
        public string $departmentId,
        public string $resourceName,
        public string $patrimonialCode,
        public ?string $manufacturer = null,
        public ?string $model = null,
        public ?string $serialNumber = null,
        public ?int $responsibleId = null,
        public ?string $notes = null,
        public ?string $documentationLink = null,
        public ?int $createdBy = null,
        public ?int $updatedBy = null,
    ) {}
}
