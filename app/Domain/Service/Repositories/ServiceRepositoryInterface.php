<?php

namespace App\Domain\Service\Repositories;

use App\Domain\Service\Entities\Service;

interface ServiceRepositoryInterface
{
    public function create(Service $service): Service;

    public function findById(string $id): ?Service;

    public function update(Service $service): ?Service;

    public function deleteById(string $id): bool;

    public function findAllPaginated(array $filters = []): array;

    /**
     * @return array<int, array{id: string, label: string}>
     */
    public function findAllForSelect(): array;
}
