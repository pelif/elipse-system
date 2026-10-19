<?php

namespace App\Domain\Status\Repositories;

use App\Domain\Status\Entities\Status;

interface StatusRepositoryInterface
{
    public function create(Status $status): Status;

    public function findById(string $id): ?Status;

    public function update(Status $status): ?Status;

    public function deleteById(string $id): bool;

    public function findAllByType(array $filters = []): array;
}

