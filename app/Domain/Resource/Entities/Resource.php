<?php

namespace App\Domain\Resource\Entities;

class Resource
{
    public function __construct(
        private ?string $id,
        private ?string $categoryId,
        private ?string $departmentId,
        private ?string $resourceName,
        private ?string $patrimonialCode,
        private ?string $manufacturer,
        private ?string $model,
        private ?string $serialNumber,
        private ?int $responsibleId,
        private ?string $notes,
        private ?string $documentationLink,
        private ?string $createdAt,
        private ?int $createdBy,
        private ?string $updatedAt,
        private ?int $updatedBy,
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function getDepartmentId(): ?string
    {
        return $this->departmentId;
    }

    public function getResourceName(): ?string
    {
        return $this->resourceName;
    }

    public function getPatrimonialCode(): ?string
    {
        return $this->patrimonialCode;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function getResponsibleId(): ?int
    {
        return $this->responsibleId;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getDocumentationLink(): ?string
    {
        return $this->documentationLink;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }
}
