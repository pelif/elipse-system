<?php

namespace App\Domain\Product\Entities;

class Product
{
    public function __construct(
        private ?string $id,
        private string $name,
        private ?string $description = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
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
