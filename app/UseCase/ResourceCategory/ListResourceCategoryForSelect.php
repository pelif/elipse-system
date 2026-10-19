<?php

namespace App\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\UseCaseInterface;

class ListResourceCategoryForSelect implements UseCaseInterface
{
    public function __construct(
        private ResourceCategoryRepositoryInterface $repository
    ) {}

    /**
     * @return array<int, array{id: string, label: string}>
     */
    public function execute(mixed $input = null): array
    {
        return $this->repository->findAllForSelect();
    }
}
