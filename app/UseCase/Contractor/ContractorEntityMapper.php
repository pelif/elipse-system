<?php

namespace App\UseCase\Contractor;

use App\Domain\Contractor\Entities\Contractor;
use App\UseCase\Contractor\DTO\CreateContractorInputDto;
use App\UseCase\Contractor\DTO\UpdateContractorInputDto;

final class ContractorEntityMapper
{
    public static function fromCreateDto(CreateContractorInputDto $input): Contractor
    {
        return new Contractor(
            id: null,
            customerId: $input->customerId,
            name: $input->name,
            phone: $input->phone,
            email: $input->email,
            communicationCare: $input->communicationCare,
            typeContact: null,
            customerName: null,
            createdBy: $input->createdBy,
            createdAt: null,
            updatedBy: $input->updatedBy,
            updatedAt: null,
        );
    }

    public static function fromUpdateDto(UpdateContractorInputDto $input): Contractor
    {
        return new Contractor(
            id: $input->id,
            customerId: $input->customerId,
            name: $input->name,
            phone: $input->phone,
            email: $input->email,
            communicationCare: $input->communicationCare,
            typeContact: null,
            customerName: null,
            createdBy: $input->createdBy,
            createdAt: null,
            updatedBy: $input->updatedBy,
            updatedAt: null,
        );
    }
}
