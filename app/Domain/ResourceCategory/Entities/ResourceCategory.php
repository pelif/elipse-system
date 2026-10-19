<?php

namespace App\Domain\ResourceCategory\Entities;

class ResourceCategory
{
    public function __construct(
        private ?string $id,
        private string $name,
        private ?string $description = null,
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
}
