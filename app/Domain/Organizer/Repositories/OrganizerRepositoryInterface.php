<?php

namespace App\Domain\Organizer\Repositories;

use App\Domain\Organizer\Entities\Organizer;

interface OrganizerRepositoryInterface
{
    public function create(Organizer $organizer): Organizer;

    public function findById(string $id): ?Organizer;

    public function update(Organizer $organizer): ?Organizer;

    public function deleteById(string $id): bool;

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, Organizer>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;
}
