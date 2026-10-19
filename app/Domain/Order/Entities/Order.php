<?php

namespace App\Domain\Order\Entities;

class Order
{
    public function __construct(
        private string $id,
        private ?string $osCode = null,
        private ?string $customerId = null,
        private ?string $organizerId = null,
        private ?string $statusId = null,
        private ?string $jobId = null,
        private ?string $contactId = null,
        private ?string $eventTitle = null,
        private ?string $osType = null,
        private ?string $startTime = null,
        private ?string $endTime = null,
        private ?int $duration = null,
        private ?string $location = null,
        private ?string $locationDetails = null,
        private ?int $numberParticipants = null,
        private ?string $communicationNotes = null,
        private ?string $productionNotes = null,
        private ?string $platformDestination = null,
        private ?string $videoEngine = null,
        private ?string $clientPlatformName = null,
        private ?string $platformLink = null,
        private ?string $fileLink = null,
        private ?string $customerLegalName = null,
        private ?string $jobCode = null,
        private ?string $jobTitle = null,
        private ?string $statusLabel = null,
        private ?string $organizerName = null,
        private ?string $organizerPhone = null,
        private ?string $organizerEmail = null,
        private ?string $contractorName = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
        private ?string $durationLabel = null,
        private ?int $createdBy = null,
        private ?int $updatedBy = null,
    ) {
        $this->id = trim($id);
        $this->osCode = $this->blankToNull($osCode);
        $this->customerId = $this->blankToNull($customerId);
        $this->organizerId = $this->blankToNull($organizerId);
        $this->statusId = $this->blankToNull($statusId);
        $this->jobId = $this->blankToNull($jobId);
        $this->contactId = $this->blankToNull($contactId);
        $this->eventTitle = $this->blankToNull($eventTitle);
        $this->osType = $this->blankToNull($osType);
        $this->startTime = $this->blankToNull($startTime);
        $this->endTime = $this->blankToNull($endTime);
        $this->location = $this->blankToNull($location);
        $this->locationDetails = $this->blankToNull($locationDetails);
        $this->communicationNotes = $this->blankToNull($communicationNotes);
        $this->productionNotes = $this->blankToNull($productionNotes);
        $this->platformDestination = $this->blankToNull($platformDestination);
        $this->videoEngine = $this->blankToNull($videoEngine);
        $this->clientPlatformName = $this->blankToNull($clientPlatformName);
        $this->platformLink = $this->blankToNull($platformLink);
        $this->fileLink = $this->blankToNull($fileLink);
        $this->customerLegalName = $this->blankToNull($customerLegalName);
        $this->jobCode = $this->blankToNull($jobCode);
        $this->jobTitle = $this->blankToNull($jobTitle);
        $this->statusLabel = $this->blankToNull($statusLabel);
        $this->organizerName = $this->blankToNull($organizerName);
        $this->organizerPhone = $this->blankToNull($organizerPhone);
        $this->organizerEmail = $this->blankToNull($organizerEmail);
        $this->contractorName = $this->blankToNull($contractorName);
        $this->createdAt = $this->blankToNull($createdAt);
        $this->updatedAt = $this->blankToNull($updatedAt);
        $this->durationLabel = $this->blankToNull($durationLabel);

        if ($this->id === '') {
            throw new \InvalidArgumentException('O identificador da OS é obrigatório.');
        }

        if ($this->numberParticipants !== null && $this->numberParticipants < 0) {
            throw new \InvalidArgumentException('O número de participantes deve ser zero ou maior.');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOsCode(): ?string
    {
        return $this->osCode;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function getOrganizerId(): ?string
    {
        return $this->organizerId;
    }

    public function getStatusId(): ?string
    {
        return $this->statusId;
    }

    public function getJobId(): ?string
    {
        return $this->jobId;
    }

    public function getContactId(): ?string
    {
        return $this->contactId;
    }

    public function getEventTitle(): ?string
    {
        return $this->eventTitle;
    }

    public function getOsType(): ?string
    {
        return $this->osType;
    }

    public function getStartTime(): ?string
    {
        return $this->startTime;
    }

    public function getEndTime(): ?string
    {
        return $this->endTime;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getLocationDetails(): ?string
    {
        return $this->locationDetails;
    }

    public function getNumberParticipants(): ?int
    {
        return $this->numberParticipants;
    }

    public function getCommunicationNotes(): ?string
    {
        return $this->communicationNotes;
    }

    public function getProductionNotes(): ?string
    {
        return $this->productionNotes;
    }

    public function getPlatformDestination(): ?string
    {
        return $this->platformDestination;
    }

    public function getVideoEngine(): ?string
    {
        return $this->videoEngine;
    }

    public function getClientPlatformName(): ?string
    {
        return $this->clientPlatformName;
    }

    public function getPlatformLink(): ?string
    {
        return $this->platformLink;
    }

    public function getFileLink(): ?string
    {
        return $this->fileLink;
    }

    public function getCustomerLegalName(): ?string
    {
        return $this->customerLegalName;
    }

    public function getJobCode(): ?string
    {
        return $this->jobCode;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function getStatusLabel(): ?string
    {
        return $this->statusLabel;
    }

    public function getOrganizerName(): ?string
    {
        return $this->organizerName;
    }

    public function getOrganizerPhone(): ?string
    {
        return $this->organizerPhone;
    }

    public function getOrganizerEmail(): ?string
    {
        return $this->organizerEmail;
    }

    public function getContractorName(): ?string
    {
        return $this->contractorName;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function getDurationLabel(): ?string
    {
        return $this->durationLabel;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }

    private function blankToNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        return $trimmed;
    }
}
