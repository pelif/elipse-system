<?php

namespace App\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteResourceCategoryUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceCategoryRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id))
            throw new \InvalidArgumentException('Input inválido para DeleteResourceCategoryUseCase');

        return $this->repository->deleteById($id);
    }
}
