<?php

namespace App\Services;

use App\UseCase\Customer\DTO\CustomerContactInputDto;
use App\UseCase\Customer\DTO\CustomerContactOutputDto;
use App\UseCase\Customer\DTO\CustomerFinancialInfoInputDto;
use App\UseCase\Customer\DTO\CustomerOutputDto;
use App\UseCase\Customer\DTO\UpdateCustomerInputDto;
use App\UseCase\Customer\GetCustomerUseCase;
use App\UseCase\Customer\UpdateCustomerUseCase;
use Illuminate\Http\Request;

final class CustomerEntryService
{

    public function __construct(
        private GetCustomerUseCase $getCustomerUseCase,
        private UpdateCustomerUseCase $updateCustomerUseCase
    ) {}


    /**
     * Updates the customer information.
     *
     * @param array $validated The validated request payload with customer information.
     * @param Request $request The current HTTP request, used for user info.
     * @param string $id The unique identifier for the customer.
     * @return CustomerOutputDto|null The updated customer output DTO or null if the customer is not found.
     */
    public function toUpdateCustomer(array $validated, Request $request, string $id): ?CustomerOutputDto
    {
        $existing = $this->getCustomerUseCase->execute($id);

        if (! $existing) return null;

        $input = new UpdateCustomerInputDto(
            id: $id,
            companyName: (string) $validated['company_name'],
            legalName: (string) $validated['legal_name'],
            document: (string) $validated['document'],
            codeCustomer: (string) $validated['code_customer'],
            active: (bool) ($validated['active'] ?? true),
            deadlinePayment: null,
            antecipation: null,
            antecipationInstrutions: null,
            createdBy: $existing->createdBy,
            updatedBy: $request->user()?->id,
            comissionedAgentId: $this->nullableString($validated['comissioned_agent_id'] ?? null),
            financialInfo: null,
            contacts: $this->listOfContacts($existing),
        );

        return $this->updateCustomerUseCase->execute($input);
    }

    /**
     * Updates the financial information of an existing customer.
     *
     * @param array $validated The validated request payload with customer financial fields.
     * @param Request $request The current HTTP request, used for user info and field analysis.
     * @param string $id The unique identifier for the customer.
     * @return mixed The result from UpdateCustomerUseCase; typically the updated CustomerOutputDto or null.
     */
    public function toUpdateFinancialInfo(array $validated, Request $request, string $id)
    {
        $existing = $this->getCustomerUseCase->execute($id);

        if (! $existing) return null;

        $antecipation = $this->hasAntecipation($request, $existing);
        $financial = $this->buildFinancialInfoInputDto($validated, $antecipation, $existing);

        $input = $this->updateDtoFromExisting($existing, [
            'updatedBy' => $request->user()?->id,
            'financialInfo' => $financial,
            'deadlinePayment' => (int) $validated['deadline_payment'],
            'antecipation' => $antecipation,
            'antecipationInstrutions' => (string) $validated['antecipation_instructions'],
        ]);

        return $this->updateCustomerUseCase->execute($input);

    }

    private function hasAntecipation(Request $request, CustomerOutputDto $existing): int
    {
        return match (true) {
            $request->has('antecipation') => $this->isAntecipation($request),
            default => $this->getAntecipation($existing)
        };
    }

    private function isAntecipation(Request $request)
    {
       return $request->boolean('antecipation') ? 1 : 0;
    }

    private function getAntecipation(CustomerOutputDto $existing): int
    {
        return (int) ($existing->financialInfo?->antecipation ?? $existing->antecipation ?? 0);
    }

    private function buildFinancialInfoInputDto(array $validated, int $antecipation, CustomerOutputDto $existing): CustomerFinancialInfoInputDto
    {
        return new CustomerFinancialInfoInputDto(
            deadlinePayment: (int) $validated['deadline_payment'],
            antecipation: $antecipation,
            antecipationInstructions: (string) $validated['antecipation_instructions'],
            emailNf: (string) $validated['email_nf'],
            financialContactName: (string) $validated['name'],
            financialContactPhone: (string) $validated['phone'],
            financialContactEmail: (string) $validated['email']
        );
    }

     private function updateDtoFromExisting(CustomerOutputDto $output, array $patch): UpdateCustomerInputDto
     {
        $financial = $patch['financialInfo'] ?? null;

        $contacts = $this->listOfContacts($output);

        return new UpdateCustomerInputDto(
            id: $output->id,
            companyName: $output->companyName,
            legalName: $output->legalName,
            document: $output->document,
            codeCustomer: $output->codeCustomer,
            active: $output->active,
            deadlinePayment: $this->setDeadlinePayment($patch, $output),
            antecipation: $this->setAntecipation($patch, $output),
            antecipationInstrutions: $this->setAntecipationInstrutions($patch, $output),
            createdBy: $output->createdBy,
            updatedBy: $patch['updatedBy'] ?? $output->updatedBy,
            comissionedAgentId: $output->comissionedAgentId,
            financialInfo: $financial,
            contacts: $contacts,
        );

     }

     private function setDeadlinePayment(array $patch, CustomerOutputDto $output): ?int
     {
         return match (true) {
             array_key_exists('deadlinePayment', $patch) => $patch['deadlinePayment'],
             default => $output->deadlinePayment,
         };
     }

    /**
     * @return array<int, CustomerContactInputDto>
     */
    private function listOfContacts(CustomerOutputDto $output): array
     {
        return array_map(
            static fn (CustomerContactOutputDto $c) => new CustomerContactInputDto(
                id: $c->id,
                name: $c->name,
                phone: $c->phone,
                email: $c->email,
                typeContact: $c->typeContact,
            ),
            $output->contacts
        );
     }

     private function setAntecipation(array $patch, CustomerOutputDto $output): int
     {
        return match (true) {
            array_key_exists('antecipation', $patch) => $patch['antecipation'],
            default => $output->antecipation
        };
     }

     private function setAntecipationInstrutions(array $patch, CustomerOutputDto $output): string
     {
        return match (true) {
            array_key_exists('antecipationInstrutions', $patch) => $patch['antecipationInstrutions'],
            default => $output->antecipationInstrutions
        };
     }

     public function nullableString(mixed $value): ?string
     {
        if ($value === null) return $value;

        $str = match (true) {
            is_string($value) => trim($value),
            !is_string($value) => (string) $value
        };

        return match (true) {
            $str === '' => null,
            default => $str
        };
     }

     public function nullableInt(mixed $value): ?int
     {
        return match (true) {
            ($value === null || $value === '') => null,
            default => (int) $value
        };
     }


}
