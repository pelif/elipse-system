<?php

namespace App\UseCase\Organizer;

use App\Domain\Organizer\Entities\Organizer;
use App\UseCase\Organizer\DTO\CreateOrganizerInputDto;
use App\UseCase\Organizer\DTO\UpdateOrganizerInputDto;

final class OrganizerEntityMapper
{
    public static function fromCreateDto(CreateOrganizerInputDto $input): Organizer
    {
        return new Organizer(
            id: null,
            customerId: $input->customerId,
            name: $input->name,
            phone: $input->phone,
            email: $input->email,
            communicationCare: $input->communicationCare,
            observations: null,
            typeContact: null,
            customerName: null,
            createdBy: $input->createdBy,
            createdAt: null,
            updatedBy: $input->updatedBy,
            updatedAt: null,
        );
    }

    public static function fromUpdateDto(UpdateOrganizerInputDto $input): Organizer
    {
        return new Organizer(
            id: $input->id,
            customerId: $input->customerId,
            name: $input->name,
            phone: $input->phone,
            email: $input->email,
            communicationCare: $input->communicationCare,
            observations: null,
            typeContact: null,
            customerName: null,
            createdBy: $input->createdBy,
            createdAt: null,
            updatedBy: $input->updatedBy,
            updatedAt: null,
        );
    }
}
