<?php

namespace App\Domain\ThirdParty\Entities;

use App\Domain\Customer\ValueObjects\Document;
use App\Domain\ThirdParty\ValueObjects\ThirdPartyJobHistory;

class ThirdParty
{
    /**
     * @param  array<int, string>  $serviceIds
     * @param  array<int, array{id: string, service: string}>  $services
     */
    public function __construct(
        private ?string $id,
        private string $name,
        private ?string $alias,
        private string $phone,
        private ?string $phoneAlternative,
        private string $email,
        private ?string $emailAlternative,
        private ?string $indication,
        private ?string $observation,
        private ?string $companyName,
        private Document $document,
        private bool $active,
        private ?int $createdBy,
        private ?string $createdAt,
        private ?int $updatedBy,
        private ?string $updatedAt,
        private array $serviceIds = [],
        private array $services = [],
        private ?ThirdPartyJobHistory $jobHistory = null,
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getPhoneAlternative(): ?string
    {
        return $this->phoneAlternative;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getEmailAlternative(): ?string
    {
        return $this->emailAlternative;
    }

    public function getIndication(): ?string
    {
        return $this->indication;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function isActive(): bool
    {
        return $this->active;
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

    /**
     * @return array<int, string>
     */
    public function getServiceIds(): array
    {
        return $this->serviceIds;
    }

    /**
     * @return array<int, array{id: string, service: string}>
     */
    public function getServices(): array
    {
        return $this->services;
    }

    public function getJobHistory(): ?ThirdPartyJobHistory
    {
        return $this->jobHistory;
    }
}
