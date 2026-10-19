<?php

namespace App\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\ResourceCategory\DTO\ResourceCategoryOutputDto;
use App\UseCase\ResourceCategory\DTO\UpdateResourceCategoryInputDto;
use App\UseCase\UseCaseInterface;

class UpdateResourceCategoryUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceCategoryRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ResourceCategoryOutputDto
    {
        if (! $input instanceof UpdateResourceCategoryInputDto)
            throw new \InvalidArgumentException('Input inválido para UpdateResourceCategoryUseCase');

        $entity = ResourceCategoryEntityMapper::fromUpdateDto($input);
        $updated = $this->repository->update($entity);

        if (! $updated) return null;

        return ResourceCategoryOutputDto::fromEntity($updated);
    }
}
