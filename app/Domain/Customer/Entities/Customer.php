<?php

namespace App\Domain\Customer\Entities;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent;
use App\Domain\Customer\ValueObjects\CustomerRelationshipsCounts;
use App\Domain\Customer\ValueObjects\Document;

class Customer
{
    /**
     * @param  array<int, ContactEntity>  $contacts
     */
    public function __construct(
        private ?string $id,
        private string $companyName,
        private string $legalName,
        private Document $document,
        private string $codeCustomer,
        private bool $active,
        private ?int $deadlinePayment,
        private ?int $antecipation,
        private ?string $antecipationInstrutions,
        private ?int $createdBy,
        private ?int $updatedBy,
        private ?string $createdAt,
        private ?string $updatedAt,
        private ?string $comissionedAgentId,
        private ?ComissionedAgent $comissionedAgent,
        private ?FinancialInfoEntity $financialInfo,
        private array $contacts,
        private ?CustomerRelationshipsCounts $relationShipsCounts,
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getLegalName(): string
    {
        return $this->legalName;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function getCodeCustomer(): string
    {
        return $this->codeCustomer;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getDeadlinePayment(): ?int
    {
        return $this->deadlinePayment;
    }

    public function getAntecipation(): ?int
    {
        return $this->antecipation;
    }

    public function getAntecipationInstrutions(): ?string
    {
        return $this->antecipationInstrutions;
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

    public function getComissionedAgentId(): ?string
    {
        return $this->comissionedAgentId;
    }

    public function getComissionedAgent(): ?ComissionedAgent
    {
        return $this->comissionedAgent;
    }

    public function getFinancialInfo(): ?FinancialInfoEntity
    {
        return $this->financialInfo;
    }

    /**
     * @return array<int, ContactEntity>
     */
    public function getContacts(): array
    {
        return $this->contacts;
    }

    public function getRelationShipsCounts(): ?CustomerRelationshipsCounts
    {
        return $this->relationShipsCounts;
    }
}
