<?php

namespace App\Domain\Resource\Repositories;

use App\Domain\Resource\Entities\Resource;

interface ResourceRepositoryInterface
{
    public function create(Resource $resource): Resource;

    public function findById(string $id): ?Resource;

    public function update(Resource $resource): ?Resource;

    public function deleteById(string $id): bool;

    /**
     * Paginated listing with column filters and optional global search.
     * Implementations should default `per_page` to 10 (Recursos grid spec).
     *
     * Typical `$filters` keys:
     * - page (int), per_page (int)
     * - sortField (string), sortDirection (asc|desc)
     * - search (string): broad match across listing columns (nome, categoria, departamento, etc.)
     * - resource_name, patrimonial_code, manufacturer, model, serial_number
     * - category_id, category (name fragment), department_id, department (name fragment)
     * - responsible (user name fragment or id, implementation-defined)
     * - notes, documentation_link
     *
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, Resource>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;
}
