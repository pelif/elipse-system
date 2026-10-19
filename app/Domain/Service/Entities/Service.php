<?php

namespace App\Domain\Service\Entities;

class Service
{
    public function __construct(
        private ?string $id,
        private string $service,
        private ?string $description = null,
        private ?string $unityId = null,
        private ?string $unity = null,
        private ?int $createdBy = null,
        private ?int $updatedBy = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getUnityId(): ?string
    {
        return $this->unityId;
    }

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function getCreator(): ?int
    {
        return $this->createdBy;
    }

    public function getEditor(): ?int
    {
        return $this->updatedBy;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
