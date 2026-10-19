<?php

namespace App\Domain\ComissionedAgent\Entities;

class ComissionedAgent
{
    public function __construct(
        private ?string $id,
        private string $name,
        private float $defaultComission,
        private int $paymentDays,
        private ?string $observations = null,
        private bool $active = true,
        private ?int $createdBy = null,
        private ?int $updatedBy = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefaultComission(): float
    {
        return $this->defaultComission;
    }

    public function getPaymentDays(): int
    {
        return $this->paymentDays;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): ?int
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
