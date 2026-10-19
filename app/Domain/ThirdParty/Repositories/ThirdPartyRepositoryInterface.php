<?php

namespace App\Domain\ThirdParty\Repositories;

use App\Domain\ThirdParty\Entities\ThirdParty;

interface ThirdPartyRepositoryInterface
{
    public function create(ThirdParty $thirdParty): ThirdParty;

    public function findById(string $id): ?ThirdParty;

    public function update(ThirdParty $thirdParty): ?ThirdParty;

    public function deleteById(string $id): bool;

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, ThirdParty>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;
}
