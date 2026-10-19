<?php

namespace App\Domain\ResourceCategory\Repositories;

use App\Domain\ResourceCategory\Entities\ResourceCategory;

interface ResourceCategoryRepositoryInterface
{
    public function create(ResourceCategory $resourceCategory): ResourceCategory;

    public function findById(string $id): ?ResourceCategory;

    public function update(ResourceCategory $resourceCategory): ?ResourceCategory;

    public function deleteById(string $id): bool;

    public function findAllForSelect(): array;

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, ResourceCategory>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;
}
