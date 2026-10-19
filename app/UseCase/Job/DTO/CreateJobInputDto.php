<?php

namespace App\UseCase\Job\DTO;

class CreateJobInputDto
{
    public function __construct(
        public string $customerId,
        public string $customerCode,
        public string $contractorId,
        public string $jobCode,
        public string $jobTitle,
        public string $productId,
        public int $osQuantity = 0,
        public ?string $briefingUrl = null,
        public ?string $observations = null,
        public ?string $targetBudget = null,
        public ?string $purchaseOrder = null,
        public ?string $purchaseOrderLink = null,
        public ?string $invoiceDueDate = null,
        public ?string $additionalInfo = null,
        public ?string $comissionedAgentId = null,
        public ?string $commissionedPercentage = null,
        public ?string $commissionPaymentDueDate = null,
        public ?int $createdBy = null,
    ) {}
}
