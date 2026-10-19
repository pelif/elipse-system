<?php

namespace App\Domain\Customer\Entities;

/**
 * Mirrors financial_info + optional UI fields when columns exist in DB.
 */
class FinancialInfoEntity
{
    public function __construct(
        private ?string $id,
        private ?string $customerId,
        private ?int $deadlinePayment,
        private ?int $antecipation,
        /** Maps to DB column spelling (financial_info uses *_instructions). */
        private ?string $antecipationInstructions,
        private ?int $createdBy,
        private ?int $updatedBy,
        private ?string $createdAt,
        private ?string $updatedAt,
        private ?string $emailNf = null,
        private ?string $financialContactName = null,
        private ?string $financialContactPhone = null,
        private ?string $financialContactEmail = null,
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function getDeadlinePayment(): ?int
    {
        return $this->deadlinePayment;
    }

    public function getAntecipation(): ?int
    {
        return $this->antecipation;
    }

    public function getAntecipationInstructions(): ?string
    {
        return $this->antecipationInstructions;
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

    public function getEmailNf(): ?string
    {
        return $this->emailNf;
    }

    public function getFinancialContactName(): ?string
    {
        return $this->financialContactName;
    }

    public function getFinancialContactPhone(): ?string
    {
        return $this->financialContactPhone;
    }

    public function getFinancialContactEmail(): ?string
    {
        return $this->financialContactEmail;
    }
}
