<?php

namespace App\Domain\ServiceUnity\Repositories;

use App\Domain\ServiceUnity\Entities\ServiceUnity;

interface ServiceUnityRepositoryInterface
{
    public function create(ServiceUnity $serviceUnity): ServiceUnity;

    public function findById(string $id): ?ServiceUnity;

    public function update(ServiceUnity $serviceUnity): ?ServiceUnity;

    public function deleteById(string $id): bool;

    public function findAllForSelect(): array;

    public function findAllPaginated(array $filters = []): array;
}
