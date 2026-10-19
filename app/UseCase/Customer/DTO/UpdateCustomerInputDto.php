<?php

namespace App\UseCase\Customer\DTO;

class UpdateCustomerInputDto
{
    /**
     * @param  array<int, CustomerContactInputDto>  $contacts
     */
    public function __construct(
        public string $id,
        public string $companyName,
        public string $legalName,
        public string $document,
        public string $codeCustomer,
        public bool $active = true,
        public ?int $deadlinePayment = null,
        public ?int $antecipation = null,
        public ?string $antecipationInstrutions = null,
        public ?int $createdBy = null,
        public ?int $updatedBy = null,
        public ?string $comissionedAgentId = null,
        public ?CustomerFinancialInfoInputDto $financialInfo = null,
        public array $contacts = [],
    ) {}
}
