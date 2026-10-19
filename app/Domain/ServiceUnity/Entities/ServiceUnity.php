<?php

namespace App\Domain\ServiceUnity\Entities;

use DateTimeImmutable;

class ServiceUnity
{
    public function __construct(
        private ?string $id,
        private string $unity,
        private ?int $createdBy,
        private ?int $updatedBy,
        private ?DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUnity(): string
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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

}
