<?php

namespace App\UseCase\Order\DTO;

use App\Domain\Order\Entities\Order;

class OrderDetailDto
{
    public function __construct(
        public string $id,
        public ?string $osCode,
        public ?string $customerId,
        public ?string $customerLegalName,
        public ?string $organizerId,
        public ?string $organizerName,
        public ?string $organizerPhone,
        public ?string $organizerEmail,
        public ?string $statusId,
        public ?string $statusLabel,
        public ?string $jobId,
        public ?string $jobCode,
        public ?string $jobTitle,
        public ?string $contactId,
        public ?string $contractorName,
        public ?string $eventTitle,
        public ?string $osType,
        public ?string $startTime,
        public ?string $endTime,
        public ?int $duration,
        public ?string $durationLabel,
        public ?string $location,
        public ?string $locationDetails,
        public ?int $numberParticipants,
        public ?string $communicationNotes,
        public ?string $productionNotes,
        public ?string $platformDestination,
        public ?string $videoEngine,
        public ?string $clientPlatformName,
        public ?string $platformLink,
        public ?string $fileLink,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromEntity(Order $order): self
    {
        return new self(
            id: $order->getId(),
            osCode: $order->getOsCode(),
            customerId: $order->getCustomerId(),
            customerLegalName: $order->getCustomerLegalName(),
            organizerId: $order->getOrganizerId(),
            organizerName: $order->getOrganizerName(),
            organizerPhone: $order->getOrganizerPhone(),
            organizerEmail: $order->getOrganizerEmail(),
            statusId: $order->getStatusId(),
            statusLabel: $order->getStatusLabel(),
            jobId: $order->getJobId(),
            jobCode: $order->getJobCode(),
            jobTitle: $order->getJobTitle(),
            contactId: $order->getContactId(),
            contractorName: $order->getContractorName(),
            eventTitle: $order->getEventTitle(),
            osType: $order->getOsType(),
            startTime: $order->getStartTime(),
            endTime: $order->getEndTime(),
            duration: $order->getDuration(),
            durationLabel: $order->getDurationLabel(),
            location: $order->getLocation(),
            locationDetails: $order->getLocationDetails(),
            numberParticipants: $order->getNumberParticipants(),
            communicationNotes: $order->getCommunicationNotes(),
            productionNotes: $order->getProductionNotes(),
            platformDestination: $order->getPlatformDestination(),
            videoEngine: $order->getVideoEngine(),
            clientPlatformName: $order->getClientPlatformName(),
            platformLink: $order->getPlatformLink(),
            fileLink: $order->getFileLink(),
            createdAt: $order->getCreatedAt(),
            updatedAt: $order->getUpdatedAt(),
        );
    }
}
