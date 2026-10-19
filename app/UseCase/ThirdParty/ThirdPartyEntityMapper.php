<?php

namespace App\UseCase\ThirdParty;

use App\Domain\Customer\ValueObjects\Document;
use App\Domain\ThirdParty\Entities\ThirdParty;
use App\UseCase\ThirdParty\DTO\CreateThirdPartyInputDto;
use App\UseCase\ThirdParty\DTO\UpdateThirdPartyInputDto;

final class ThirdPartyEntityMapper
{
    public static function fromCreateDto(CreateThirdPartyInputDto $input): ThirdParty
    {
        $document = Document::fromString($input->document);

        return new ThirdParty(
            id: null,
            name: $input->name,
            alias: $input->alias,
            phone: $input->phone,
            phoneAlternative: $input->phoneAlternative,
            email: $input->email,
            emailAlternative: $input->emailAlternative,
            indication: $input->indication,
            observation: $input->observation,
            companyName: $input->companyName,
            document: $document,
            active: $input->active,
            createdBy: $input->createdBy,
            createdAt: null,
            updatedBy: $input->updatedBy,
            updatedAt: null,
            serviceIds: $input->serviceIds,
            services: [],
            jobHistory: null,
        );
    }

    public static function fromUpdateDto(UpdateThirdPartyInputDto $input): ThirdParty
    {
        $document = Document::fromString($input->document);

        return new ThirdParty(
            id: $input->id,
            name: $input->name,
            alias: $input->alias,
            phone: $input->phone,
            phoneAlternative: $input->phoneAlternative,
            email: $input->email,
            emailAlternative: $input->emailAlternative,
            indication: $input->indication,
            observation: $input->observation,
            companyName: $input->companyName,
            document: $document,
            active: $input->active,
            createdBy: $input->createdBy,
            createdAt: null,
            updatedBy: $input->updatedBy,
            updatedAt: null,
            serviceIds: $input->serviceIds,
            services: [],
            jobHistory: null,
        );
    }
}
