<?php

namespace App\Domain\Customer\Entities;

class ContactEntity
{
    public function __construct(
        private ?string $id,
        private ?string $customerId,
        private ?string $name,
        private ?string $phone,
        private ?string $email,
        private ?string $typeContact,
        private ?int $createdBy,
        private ?int $updatedBy,
        private ?string $createdAt,
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTypeContact(): ?string
    {
        return $this->typeContact;
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
