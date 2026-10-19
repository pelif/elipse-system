<?php

namespace App\UseCase\Resource;

use App\Domain\Resource\Entities\Resource;
use App\UseCase\Resource\DTO\CreateResourceInputDto;
use App\UseCase\Resource\DTO\UpdateResourceInputDto;

final class ResourceEntityMapper
{
    public static function fromCreateDto(CreateResourceInputDto $input): Resource
    {
        return new Resource(
            id: null,
            categoryId: $input->categoryId,
            departmentId: $input->departmentId,
            resourceName: $input->resourceName,
            patrimonialCode: $input->patrimonialCode,
            manufacturer: $input->manufacturer,
            model: $input->model,
            serialNumber: $input->serialNumber,
            responsibleId: $input->responsibleId,
            notes: $input->notes,
            documentationLink: $input->documentationLink,
            createdAt: null,
            createdBy: $input->createdBy,
            updatedAt: null,
            updatedBy: $input->updatedBy,
        );
    }

    public static function fromUpdateDto(UpdateResourceInputDto $input): Resource
    {
        return new Resource(
            id: $input->id,
            categoryId: $input->categoryId,
            departmentId: $input->departmentId,
            resourceName: $input->resourceName,
            patrimonialCode: $input->patrimonialCode,
            manufacturer: $input->manufacturer,
            model: $input->model,
            serialNumber: $input->serialNumber,
            responsibleId: $input->responsibleId,
            notes: $input->notes,
            documentationLink: $input->documentationLink,
            createdAt: null,
            createdBy: $input->createdBy,
            updatedAt: null,
            updatedBy: $input->updatedBy,
        );
    }
}
