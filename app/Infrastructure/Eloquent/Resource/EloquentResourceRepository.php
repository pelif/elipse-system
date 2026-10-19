<?php

namespace App\Infrastructure\Eloquent\Resource;

use App\Domain\Resource\Entities\Resource as ResourceEntity;
use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\Models\Resource as ResourceModel;
use DateTimeInterface;
use Illuminate\Support\Str;

class EloquentResourceRepository implements ResourceRepositoryInterface
{
    public function create(ResourceEntity $resource): ResourceEntity
    {
        $id = $resource->getId();
        if ($id !== null && trim((string) $id) !== '') {
            $id = trim((string) $id);
        } else {
            $id = (string) Str::uuid();
        }

        $model = ResourceModel::forceCreate(array_merge([
            'id' => $id,
        ], $this->persistAttributesFromEntity($resource)));

        return $this->toEntity($model->fresh());
    }

    public function findById(string $id): ?ResourceEntity
    {
        $model = ResourceModel::query()->find(trim($id));

        if (! $model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function update(ResourceEntity $resource): ?ResourceEntity
    {
        $id = $resource->getId();
        if ($id === null || trim((string) $id) === '') {
            return null;
        }

        $model = ResourceModel::query()->find(trim((string) $id));

        if (! $model) {
            return null;
        }

        $model->update($this->persistAttributesFromEntity($resource));

        return $this->toEntity($model->fresh());
    }

    public function deleteById(string $id): bool
    {
        $id = trim($id);
        if ($id === '') {
            return false;
        }

        $model = ResourceModel::query()->find($id);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function findAllPaginated(array $filters = []): array
    {
        $filters = $filters ?? [];

        $sortField = $filters['sortField'] ?? 'resource_name';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = [
            'id',
            'resource_name',
            'patrimonial_code',
            'manufacturer',
            'model',
            'serial_number',
            'notes',
            'documentation_link',
            'category_id',
            'department_id',
            'responsible_id',
            'created_at',
            'updated_at',
        ];
        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'resource_name';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (! in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $resourceName = $filters['resource_name'] ?? $filters['name'] ?? null;
        $patrimonialCode = $filters['patrimonial_code'] ?? null;
        $manufacturer = $filters['manufacturer'] ?? null;
        $modelName = $filters['model'] ?? null;
        $serialNumber = $filters['serial_number'] ?? null;
        $categoryId = $filters['category_id'] ?? null;
        $categoryName = $filters['category'] ?? null;
        $departmentId = $filters['department_id'] ?? null;
        $departmentName = $filters['department'] ?? null;
        $responsibleFilter = $filters['responsible'] ?? null;
        $notes = $filters['notes'] ?? null;
        $documentationLink = $filters['documentation_link'] ?? null;
        $search = $filters['search'] ?? null;

        $query = ResourceModel::query();

        if (! empty($resourceName)) {
            $query->where('resource_name', 'like', '%'.$resourceName.'%');
        }

        if (! empty($patrimonialCode)) {
            $query->where('patrimonial_code', 'like', '%'.$patrimonialCode.'%');
        }

        if (! empty($manufacturer)) {
            $query->where('manufacturer', 'like', '%'.$manufacturer.'%');
        }

        if (! empty($modelName)) {
            $query->where('model', 'like', '%'.$modelName.'%');
        }

        if (! empty($serialNumber)) {
            $query->where('serial_number', 'like', '%'.$serialNumber.'%');
        }

        if (! empty($categoryId)) {
            $query->where('category_id', trim((string) $categoryId));
        }

        if (! empty($categoryName)) {
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', 'like', '%'.$categoryName.'%');
            });
        }

        if (! empty($departmentId)) {
            $query->where('department_id', trim((string) $departmentId));
        }

        if (! empty($departmentName)) {
            $query->whereHas('department', function ($q) use ($departmentName) {
                $q->where('department', 'like', '%'.$departmentName.'%');
            });
        }

        if (! empty($responsibleFilter)) {
            $r = trim((string) $responsibleFilter);
            if (ctype_digit($r)) {
                $query->where('responsible_id', (int) $r);
            } else {
                $query->whereHas('responsible', function ($q) use ($r) {
                    $q->where('name', 'like', '%'.$r.'%')
                        ->orWhere('email', 'like', '%'.$r.'%');
                });
            }
        }

        if (! empty($notes)) {
            $query->where('notes', 'like', '%'.$notes.'%');
        }

        if (! empty($documentationLink)) {
            $query->where('documentation_link', 'like', '%'.$documentationLink.'%');
        }

        if (! empty($search)) {
            $s = (string) $search;
            $query->where(function ($q) use ($s) {
                $q->where('resource_name', 'like', '%'.$s.'%')
                    ->orWhere('patrimonial_code', 'like', '%'.$s.'%')
                    ->orWhere('manufacturer', 'like', '%'.$s.'%')
                    ->orWhere('model', 'like', '%'.$s.'%')
                    ->orWhere('serial_number', 'like', '%'.$s.'%')
                    ->orWhere('notes', 'like', '%'.$s.'%')
                    ->orWhere('documentation_link', 'like', '%'.$s.'%')
                    ->orWhereHas('category', function ($cq) use ($s) {
                        $cq->where('name', 'like', '%'.$s.'%');
                    })
                    ->orWhereHas('department', function ($dq) use ($s) {
                        $dq->where('department', 'like', '%'.$s.'%');
                    })
                    ->orWhereHas('responsible', function ($rq) use ($s) {
                        $rq->where('name', 'like', '%'.$s.'%')
                            ->orWhere('email', 'like', '%'.$s.'%');
                    });
            });
        }

        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : null;

        $perPage = 10;
        if (isset($filters['per_page'])) {
            $candidate = (int) $filters['per_page'];
            if ($candidate < 1) {
                $perPage = 1;
            } elseif ($candidate > 100) {
                $perPage = 100;
            } else {
                $perPage = $candidate;
            }
        }

        $paginator = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(perPage: $perPage, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator->getCollection()->map(fn (ResourceModel $model) => $this->toEntity($model))->toArray(),
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
    private function persistAttributesFromEntity(ResourceEntity $resource): array
    {
        return [
            'category_id' => $resource->getCategoryId(),
            'department_id' => $resource->getDepartmentId(),
            'resource_name' => $resource->getResourceName(),
            'patrimonial_code' => $resource->getPatrimonialCode(),
            'manufacturer' => $resource->getManufacturer(),
            'model' => $resource->getModel(),
            'serial_number' => $resource->getSerialNumber(),
            'responsible_id' => $resource->getResponsibleId(),
            'notes' => $resource->getNotes(),
            'documentation_link' => $resource->getDocumentationLink(),
            'created_by' => $resource->getCreatedBy(),
            'updated_by' => $resource->getUpdatedBy(),
        ];
    }

    private function toEntity(ResourceModel $model): ResourceEntity
    {
        return new ResourceEntity(
            id: $model->id,
            categoryId: $model->category_id,
            departmentId: $model->department_id,
            resourceName: $model->resource_name,
            patrimonialCode: $model->patrimonial_code,
            manufacturer: $model->manufacturer,
            model: $model->model,
            serialNumber: $model->serial_number,
            responsibleId: $model->responsible_id !== null ? (int) $model->responsible_id : null,
            notes: $model->notes,
            documentationLink: $model->documentation_link,
            createdAt: $model->created_at?->format(DateTimeInterface::ATOM),
            createdBy: $model->created_by !== null ? (int) $model->created_by : null,
            updatedAt: $model->updated_at?->format(DateTimeInterface::ATOM),
            updatedBy: $model->updated_by !== null ? (int) $model->updated_by : null,
        );
    }
}
