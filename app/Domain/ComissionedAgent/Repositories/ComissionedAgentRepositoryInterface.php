<?php

namespace App\Domain\ComissionedAgent\Repositories;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent;

interface ComissionedAgentRepositoryInterface
{
    public function create(ComissionedAgent $comissionedAgent): ComissionedAgent;

    public function findById(string $id): ?ComissionedAgent;

    public function update(ComissionedAgent $comissionedAgent): ?ComissionedAgent;

    public function deleteById(string $id): bool;

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, ComissionedAgent>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;

    public function findAllForSelect(): array;
}
