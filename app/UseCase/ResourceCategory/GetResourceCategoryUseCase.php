<?php

namespace App\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\ResourceCategory\DTO\ResourceCategoryOutputDto;
use App\UseCase\UseCaseInterface;

class GetResourceCategoryUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceCategoryRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ResourceCategoryOutputDto
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id)) throw new \InvalidArgumentException('Input inválido para GetResourceCategoryUseCase');

        $entity = $this->repository->findById($id);

        if (! $entity) return null;

        return ResourceCategoryOutputDto::fromEntity($entity);
    }
}
