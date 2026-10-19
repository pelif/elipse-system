<?php

namespace App\UseCase\Order\DTO;

use App\Domain\Order\Entities\Order;

class OrderListItemDto
{
    public function __construct(
        public string $id,
        public ?string $osCode,
        public ?string $eventTitle,
        public ?string $jobId,
        public ?string $jobCode,
        public ?string $customerId,
        public ?string $customerLegalName,
        public ?string $statusId,
        public ?string $statusLabel,
        public ?string $startTime,
        public ?string $endTime,
        public ?string $durationLabel,
        public ?string $location,
        public ?string $osType,
        public ?string $organizerName,
        public ?string $contractorName,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromEntity(Order $order): self
    {
        return new self(
            id: $order->getId(),
            osCode: $order->getOsCode(),
            eventTitle: $order->getEventTitle(),
            jobId: $order->getJobId(),
            jobCode: $order->getJobCode(),
            customerId: $order->getCustomerId(),
            customerLegalName: $order->getCustomerLegalName(),
            statusId: $order->getStatusId(),
            statusLabel: $order->getStatusLabel(),
            startTime: $order->getStartTime(),
            endTime: $order->getEndTime(),
            durationLabel: $order->getDurationLabel(),
            location: $order->getLocation(),
            osType: $order->getOsType(),
            organizerName: $order->getOrganizerName(),
            contractorName: $order->getContractorName(),
            createdAt: $order->getCreatedAt(),
            updatedAt: $order->getUpdatedAt(),
        );
    }
}
