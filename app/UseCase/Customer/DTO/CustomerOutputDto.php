<?php

namespace App\UseCase\Customer\DTO;

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Entities\ContactEntity;
use App\Domain\Customer\ValueObjects\CustomerRelationshipsCounts;
use App\UseCase\ComissionedAgent\DTO\ComissionedAgentOutputDto;

class CustomerOutputDto
{
    /**
     * @param  array<int, CustomerContactOutputDto>  $contacts
     */
    public function __construct(
        public string $id,
        public string $companyName,
        public string $legalName,
        public string $document,
        public string $codeCustomer,
        public bool $active,
        public ?int $deadlinePayment,
        public ?int $antecipation,
        public ?string $antecipationInstrutions,
        public ?int $createdBy,
        public ?int $updatedBy,
        public ?string $createdAt,
        public ?string $updatedAt,
        public ?string $comissionedAgentId,
        public ?ComissionedAgentOutputDto $comissionedAgent,
        public ?CustomerFinancialInfoOutputDto $financialInfo,
        public array $contacts,
        public int $jobsCount,
        public int $contractorsCount,
        public int $organizersCount,
    ) {}

    public static function fromEntity(Customer $customer): self
    {
        $id = $customer->getId();
        if ($id === null || $id === '') {
            throw new \LogicException('Customer must have an id to build CustomerOutputDto.');
        }

        $contacts = array_map(
            static fn (ContactEntity $contact) => CustomerContactOutputDto::fromEntity($contact),
            $customer->getContacts()
        );

        $relationshipCounts = $customer->getRelationShipsCounts();
        if ($relationshipCounts === null) {
            $relationshipCounts = CustomerRelationshipsCounts::zero();
        }

        return new self(
            id: $id,
            companyName: $customer->getCompanyName(),
            legalName: $customer->getLegalName(),
            document: $customer->getDocument()->getValue(),
            codeCustomer: $customer->getCodeCustomer(),
            active: $customer->isActive(),
            deadlinePayment: $customer->getDeadlinePayment(),
            antecipation: $customer->getAntecipation(),
            antecipationInstrutions: $customer->getAntecipationInstrutions(),
            createdBy: $customer->getCreatedBy(),
            updatedBy: $customer->getUpdatedBy(),
            createdAt: $customer->getCreatedAt(),
            updatedAt: $customer->getUpdatedAt(),
            comissionedAgentId: $customer->getComissionedAgentId(),
            comissionedAgent: ComissionedAgentOutputDto::fromEntity($customer->getComissionedAgent()),
            financialInfo: CustomerFinancialInfoOutputDto::fromEntity($customer->getFinancialInfo()),
            contacts: $contacts,
            jobsCount: $relationshipCounts->getJobs(),
            contractorsCount: $relationshipCounts->getContractors(),
            organizersCount: $relationshipCounts->getOrganizers(),
        );
    }
}
