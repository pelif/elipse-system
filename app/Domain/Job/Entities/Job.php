<?php

namespace App\Domain\Job\Entities;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Job\ValueObjects\BriefingUrl;
use App\Domain\Job\ValueObjects\JobCode;
use App\Domain\Product\Entities\Product;
use App\Domain\Status\Entities\Status;

class Job
{
    public function __construct(
        private string $customerId,
        private string $productId,
        private JobCode $jobCode,
        private string $jobTitle,
        private string $contractorId,
        private ?string $id = null,
        private ?Customer $customer = null,
        private ?Product $product = null,
        private ?BriefingUrl $briefingUrl = null,
        private ?string $approvalAr = null,
        private ?string $statusId = null,
        private ?Status $status = null,
        private ?int $createdBy = null,
        private ?string $createdByName = null,
        private ?int $updatedBy = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
        private int $osQuantity = 0,
        private ?string $observations = null,
        private ?string $approvalDate = null,
        private ?string $targetBudget = null,
        private ?string $purchaseOrder = null,
        private ?string $purchaseOrderLink = null,
        private ?string $invoiceDueDate = null,
        private ?string $additionalInfo = null,
        private ?string $comissionedAgentId = null,
        private ?ComissionedAgent $comissionedAgent = null,
        private ?string $commissionedPercentage = null,
        private ?string $commissionValue = null,
        private ?string $commissionPaymentDueDate = null,
        private ?string $taxPercentage = null,
        private ?string $profitPercentage = null,
        private ?string $monthlyInterestRate = null,
        private ?string $dailyInterestRate = null,
        private string $adminExpensesPercentage = '0.0000',
        private string $financialExpensesPercentage = '0.0000',
        private ?string $contractorName = null,
    ) {
        $this->customerId = trim($customerId);
        $this->productId = trim($productId);
        $this->contractorId = trim($contractorId);
        $this->jobTitle = trim($jobTitle);

        if ($this->customerId === '' || $this->productId === '' || $this->contractorId === '') {
            throw new \InvalidArgumentException('Cliente, produto e contratante são obrigatórios.');
        }

        if ($this->jobTitle === '') {
            throw new \InvalidArgumentException('O título do job é obrigatório.');
        }

        if ($this->osQuantity < 0) {
            throw new \InvalidArgumentException('A quantidade de OS deve ser zero ou maior.');
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getJobCode(): JobCode
    {
        return $this->jobCode;
    }

    public function getJobTitle(): string
    {
        return $this->jobTitle;
    }

    public function getContractorId(): string
    {
        return $this->contractorId;
    }

    public function getContractorName(): ?string
    {
        return $this->contractorName;
    }

    public function getBriefingUrl(): ?BriefingUrl
    {
        return $this->briefingUrl;
    }

    public function getApprovalAr(): ?string
    {
        return $this->approvalAr;
    }

    public function getStatusId(): ?string
    {
        return $this->statusId;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function getCreatedByName(): ?string
    {
        return $this->createdByName;
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

    public function getOsQuantity(): int
    {
        return $this->osQuantity;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function getApprovalDate(): ?string
    {
        return $this->approvalDate;
    }

    public function getTargetBudget(): ?string
    {
        return $this->targetBudget;
    }

    public function getPurchaseOrder(): ?string
    {
        return $this->purchaseOrder;
    }

    public function getPurchaseOrderLink(): ?string
    {
        return $this->purchaseOrderLink;
    }

    public function getInvoiceDueDate(): ?string
    {
        return $this->invoiceDueDate;
    }

    public function getAdditionalInfo(): ?string
    {
        return $this->additionalInfo;
    }

    public function getComissionedAgentId(): ?string
    {
        return $this->comissionedAgentId;
    }

    public function getComissionedAgent(): ?ComissionedAgent
    {
        return $this->comissionedAgent;
    }

    public function getCommissionedPercentage(): ?string
    {
        return $this->commissionedPercentage;
    }

    public function getCommissionValue(): ?string
    {
        return $this->commissionValue;
    }

    public function getCommissionPaymentDueDate(): ?string
    {
        return $this->commissionPaymentDueDate;
    }

    public function getTaxPercentage(): ?string
    {
        return $this->taxPercentage;
    }

    public function getProfitPercentage(): ?string
    {
        return $this->profitPercentage;
    }

    public function getMonthlyInterestRate(): ?string
    {
        return $this->monthlyInterestRate;
    }

    public function getDailyInterestRate(): ?string
    {
        return $this->dailyInterestRate;
    }

    public function getAdminExpensesPercentage(): string
    {
        return $this->adminExpensesPercentage;
    }

    public function getFinancialExpensesPercentage(): string
    {
        return $this->financialExpensesPercentage;
    }
}
