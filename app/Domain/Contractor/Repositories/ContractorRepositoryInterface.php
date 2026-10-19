<?php

namespace App\Domain\Contractor\Repositories;

use App\Domain\Contractor\Entities\Contractor;

interface ContractorRepositoryInterface
{
    public function create(Contractor $contractor): Contractor;

    public function findById(string $id): ?Contractor;

    public function update(Contractor $contractor): ?Contractor;

    public function deleteById(string $id): bool;

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, Contractor>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;
}
