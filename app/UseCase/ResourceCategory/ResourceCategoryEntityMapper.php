<?php

namespace App\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Entities\ResourceCategory;
use App\UseCase\ResourceCategory\DTO\CreateResourceCategoryInputDto;
use App\UseCase\ResourceCategory\DTO\UpdateResourceCategoryInputDto;

final class ResourceCategoryEntityMapper
{
    public static function fromCreateDto(CreateResourceCategoryInputDto $input): ResourceCategory
    {
        return new ResourceCategory(
            id: null,
            name: $input->name,
            description: $input->description,
        );
    }

    public static function fromUpdateDto(UpdateResourceCategoryInputDto $input): ResourceCategory
    {
        return new ResourceCategory(
            id: $input->id,
            name: $input->name,
            description: $input->description,
        );
    }
}
