<?php

namespace App\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\ResourceCategory\DTO\CreateResourceCategoryInputDto;
use App\UseCase\ResourceCategory\DTO\ResourceCategoryOutputDto;
use App\UseCase\UseCaseInterface;

class CreateResourceCategoryUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceCategoryRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ResourceCategoryOutputDto
    {
        if (! $input instanceof CreateResourceCategoryInputDto)
            throw new \InvalidArgumentException('Input inválido para CreateResourceCategoryUseCase');

        $entity = ResourceCategoryEntityMapper::fromCreateDto($input);
        $created = $this->repository->create($entity);

        return ResourceCategoryOutputDto::fromEntity($created);
    }
}
