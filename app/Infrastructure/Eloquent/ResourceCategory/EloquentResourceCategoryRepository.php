<?php

namespace App\Infrastructure\Eloquent\ResourceCategory;

use App\Domain\ResourceCategory\Entities\ResourceCategory;
use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\Models\ResourceCategory as ResourceCategoryModel;
use Illuminate\Support\Str;

class EloquentResourceCategoryRepository implements ResourceCategoryRepositoryInterface
{
    public function create(ResourceCategory $resourceCategory): ResourceCategory
    {
        $rawId = $resourceCategory->getId();
        $resolvedId = (string) Str::uuid();
        if ($rawId !== null && trim((string) $rawId) !== '') {
            $resolvedId = trim((string) $rawId);
        }

        $model = ResourceCategoryModel::forceCreate(array_merge([
            'id' => $resolvedId,
        ], $this->persistAttributesFromEntity($resourceCategory)));

        return $this->toEntity($model);
    }

    public function findById(string $id): ?ResourceCategory
    {
        $trimmed = trim($id);
        if ($trimmed === '') return null;

        $model = ResourceCategoryModel::query()->find($trimmed);

        if (! $model) return null;

        return $this->toEntity($model);
    }

    public function update(ResourceCategory $resourceCategory): ?ResourceCategory
    {
        $id = $resourceCategory->getId();
        if ($id === null || trim((string) $id) === '')
            return null;

        $model = ResourceCategoryModel::query()->find(trim((string) $id));

        if (! $model) return null;

        $model->update($this->persistAttributesFromEntity($resourceCategory));

        return $this->toEntity($model->fresh());
    }

    public function deleteById(string $id): bool
    {
        $trimmed = trim($id);
        if ($trimmed === '') return false;

        $model = ResourceCategoryModel::query()->find($trimmed);

        if (! $model) return false;

        return (bool) $model->delete();
    }


    /**
     * @return array<int, array{id: string, label: string}>
     */
    public function findAllForSelect(): array
    {
        $rows = ResourceCategoryModel::query()
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'id' => (string) $row->id,
                'label' => (string) $row->name,
            ];
        }

        return $out;
    }

    public function findAllPaginated(array $filters = []): array
    {
        $sortField = $filters['sortField'] ?? 'name';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = [
            'id',
            'name',
            'description',
        ];
        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'name';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (! in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $name = $filters['name'] ?? null;
        $description = $filters['description'] ?? null;
        $search = $filters['search'] ?? null;

        $query = ResourceCategoryModel::query();

        if (! empty($name)) {
            $query->where('name', 'like', '%'.$name.'%');
        }

        if (! empty($description)) {
            $query->where('description', 'like', '%'.$description.'%');
        }

        if (! empty($search)) {
            $s = (string) $search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', '%'.$s.'%')
                    ->orWhere('description', 'like', '%'.$s.'%');
            });
        }

        $query->orderBy($sortField, $sortDirection);

        $page = null;
        if (isset($filters['page'])) {
            $page = max(1, (int) $filters['page']);
        }

        $paginator = $query
            ->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator->getCollection()->map(fn (ResourceCategoryModel $model) => $this->toEntity($model))->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function persistAttributesFromEntity(ResourceCategory $resourceCategory): array
    {
        return [
            'name' => $resourceCategory->getName(),
            'description' => $resourceCategory->getDescription(),
        ];
    }

    private function toEntity(ResourceCategoryModel $model): ResourceCategory
    {
        return new ResourceCategory(
            id: $model->id,
            name: (string) ($model->name ?? ''),
            description: $model->description,
        );
    }
}
