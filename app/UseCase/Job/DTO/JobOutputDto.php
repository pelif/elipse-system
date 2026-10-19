<?php

namespace App\UseCase\Job\DTO;

use App\Domain\Job\Entities\Job;

class JobOutputDto
{
    public function __construct(
        public string $id,
        public string $customerId,
        public ?string $customerName,
        public ?string $customerCode,
        public string $contractorId,
        public ?string $contractorName,
        public string $jobCode,
        public string $jobTitle,
        public string $productId,
        public ?string $productName,
        public int $osQuantity,
        public ?string $statusId,
        public ?string $statusName,
        public ?string $briefingUrl,
        public ?string $observations,
        public ?string $targetBudget,
        public ?string $purchaseOrder,
        public ?string $purchaseOrderLink,
        public ?string $invoiceDueDate,
        public ?string $additionalInfo,
        public ?string $comissionedAgentId,
        public ?string $comissionedAgentName,
        public ?string $commissionedPercentage,
        public ?string $commissionPaymentDueDate,
        public ?int $createdBy,
        public ?string $createdByName,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromEntity(Job $job): self
    {
        $id = $job->getId();
        if ($id === null || $id === '') {
            throw new \LogicException('Job must have an id to build JobOutputDto.');
        }

        $customerName = null;
        $customerCode = null;
        if ($job->getCustomer() !== null) {
            $customerName = $job->getCustomer()->getCompanyName();
            $customerCode = $job->getCustomer()->getCodeCustomer();
        }

        $productName = null;
        if ($job->getProduct() !== null) {
            $productName = $job->getProduct()->getName();
        }

        $statusName = null;
        if ($job->getStatus() !== null) {
            $statusName = $job->getStatus()->getStatus();
        }

        $agentName = null;
        if ($job->getComissionedAgent() !== null) {
            $agentName = $job->getComissionedAgent()->getName();
        }

        $briefingUrl = null;
        if ($job->getBriefingUrl() !== null) {
            $briefingUrl = $job->getBriefingUrl()->getValue();
        }

        return new self(
            id: $id,
            customerId: $job->getCustomerId(),
            customerName: $customerName,
            customerCode: $customerCode,
            contractorId: $job->getContractorId(),
            contractorName: $job->getContractorName(),
            jobCode: $job->getJobCode()->getValue(),
            jobTitle: $job->getJobTitle(),
            productId: $job->getProductId(),
            productName: $productName,
            osQuantity: $job->getOsQuantity(),
            statusId: $job->getStatusId(),
            statusName: $statusName,
            briefingUrl: $briefingUrl,
            observations: $job->getObservations(),
            targetBudget: $job->getTargetBudget(),
            purchaseOrder: $job->getPurchaseOrder(),
            purchaseOrderLink: $job->getPurchaseOrderLink(),
            invoiceDueDate: $job->getInvoiceDueDate(),
            additionalInfo: $job->getAdditionalInfo(),
            comissionedAgentId: $job->getComissionedAgentId(),
            comissionedAgentName: $agentName,
            commissionedPercentage: $job->getCommissionedPercentage(),
            commissionPaymentDueDate: $job->getCommissionPaymentDueDate(),
            createdBy: $job->getCreatedBy(),
            createdByName: $job->getCreatedByName(),
            createdAt: $job->getCreatedAt(),
            updatedAt: $job->getUpdatedAt(),
        );
    }
}
