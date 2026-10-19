<?php

namespace App\UseCase\ResourceCategory\DTO;

use App\Domain\ResourceCategory\Entities\ResourceCategory;

class ResourceCategoryOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
    ) {}

    public static function fromEntity(ResourceCategory $resourceCategory): self
    {
        $id = $resourceCategory->getId();

        if ($id === null || $id === '') {
            throw new \LogicException('ResourceCategory must have an id to build ResourceCategoryOutputDto.');
        }

        return new self(
            id: $id,
            name: $resourceCategory->getName(),
            description: $resourceCategory->getDescription(),
        );
    }
}
