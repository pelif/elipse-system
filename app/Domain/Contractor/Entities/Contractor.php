<?php

namespace App\Domain\Contractor\Entities;

class Contractor
{
    public function __construct(
        private ?string $id,
        private ?string $customerId,
        private string $name,
        private string $phone,
        private string $email,
        private ?string $communicationCare,
        private ?string $typeContact,
        private ?string $customerName,
        private ?int $createdBy,
        private ?string $createdAt,
        private ?int $updatedBy,
        private ?string $updatedAt,
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCommunicationCare(): ?string
    {
        return $this->communicationCare;
    }

    public function getTypeContact(): ?string
    {
        return $this->typeContact;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
