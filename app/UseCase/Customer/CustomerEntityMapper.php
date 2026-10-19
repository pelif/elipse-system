<?php

namespace App\UseCase\Customer;

use App\Domain\Customer\Entities\ContactEntity;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Entities\FinancialInfoEntity;
use App\Domain\Customer\ValueObjects\Document;
use App\UseCase\Customer\DTO\CreateCustomerInputDto;
use App\UseCase\Customer\DTO\CustomerContactInputDto;
use App\UseCase\Customer\DTO\CustomerFinancialInfoInputDto;
use App\UseCase\Customer\DTO\UpdateCustomerInputDto;

/**
 * Maps HTTP/application DTOs into domain {@see Customer} aggregates.
 */
final class CustomerEntityMapper
{
    public static function fromCreateDto(CreateCustomerInputDto $input): Customer
    {
        $document = Document::fromString($input->document);

        return new Customer(
            id: null,
            companyName: $input->companyName,
            legalName: $input->legalName,
            document: $document,
            codeCustomer: $input->codeCustomer,
            active: $input->active,
            deadlinePayment: $input->deadlinePayment,
            antecipation: $input->antecipation,
            antecipationInstrutions: $input->antecipationInstrutions,
            createdBy: $input->createdBy,
            updatedBy: $input->updatedBy,
            createdAt: null,
            updatedAt: null,
            comissionedAgentId: $input->comissionedAgentId,
            comissionedAgent: null,
            financialInfo: self::financialFromInput($input->financialInfo),
            contacts: self::contactsFromInput($input->contacts),
            relationShipsCounts: null,
        );
    }

    public static function fromUpdateDto(UpdateCustomerInputDto $input): Customer
    {
        $document = Document::fromString($input->document);

        return new Customer(
            id: $input->id,
            companyName: $input->companyName,
            legalName: $input->legalName,
            document: $document,
            codeCustomer: $input->codeCustomer,
            active: $input->active,
            deadlinePayment: $input->deadlinePayment,
            antecipation: $input->antecipation,
            antecipationInstrutions: $input->antecipationInstrutions,
            createdBy: $input->createdBy,
            updatedBy: $input->updatedBy,
            createdAt: null,
            updatedAt: null,
            comissionedAgentId: $input->comissionedAgentId,
            comissionedAgent: null,
            financialInfo: self::financialFromInput($input->financialInfo),
            contacts: self::contactsFromInput($input->contacts),
            relationShipsCounts: null,
        );
    }

    /**
     * @param  array<int, CustomerContactInputDto>  $items
     * @return array<int, ContactEntity>
     */
    private static function contactsFromInput(array $items): array
    {
        $out = [];

        foreach ($items as $dto) {
            if (!$dto instanceof CustomerContactInputDto) {
                throw new \InvalidArgumentException('Each contact entry must be a CustomerContactInputDto.');
            }

            $out[] = new ContactEntity(
                id: $dto->id,
                customerId: null,
                name: $dto->name,
                phone: $dto->phone,
                email: $dto->email,
                typeContact: $dto->typeContact,
                createdBy: null,
                updatedBy: null,
                createdAt: null,
                updatedAt: null,
            );
        }

        return $out;
    }

    private static function financialFromInput(?CustomerFinancialInfoInputDto $dto): ?FinancialInfoEntity
    {
        if ($dto === null) {
            return null;
        }

        return new FinancialInfoEntity(
            id: null,
            customerId: null,
            deadlinePayment: $dto->deadlinePayment,
            antecipation: $dto->antecipation,
            antecipationInstructions: $dto->antecipationInstructions,
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null,
            emailNf: $dto->emailNf,
            financialContactName: $dto->financialContactName,
            financialContactPhone: $dto->financialContactPhone,
            financialContactEmail: $dto->financialContactEmail,
        );
    }
}
