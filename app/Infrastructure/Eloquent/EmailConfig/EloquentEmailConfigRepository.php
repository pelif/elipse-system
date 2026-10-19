<?php

namespace App\Infrastructure\Eloquent\EmailConfig;

use App\Domain\EmailConfig\Entities\EmailConfig as EmailConfigEntity;
use App\Domain\EmailConfig\Repositories\EmailConfigRepositoryInterface;
use App\Models\EmailSetting as EmailSettingModel;
use Illuminate\Database\Eloquent\Builder;

class EloquentEmailConfigRepository implements EmailConfigRepositoryInterface
{
    public function create(EmailConfigEntity $emailConfig): EmailConfigEntity
    {
        $model = EmailSettingModel::create([
            'department_id' => $emailConfig->getDepartmentId(),
            'address' => $emailConfig->getEmail(),
        ]);

        return $this->toEntity($model);
    }

    public function findById(string $id): ?EmailConfigEntity
    {
        $model = EmailSettingModel::query()->with('department')->find($id);

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function update(EmailConfigEntity $emailConfig): ?EmailConfigEntity
    {
        $id = $emailConfig->getId();
        if (empty($id)) {
            return null;
        }

        $model = EmailSettingModel::query()->find($id);
        if (!$model) {
            return null;
        }

        $model->update([
            'department_id' => $emailConfig->getDepartmentId(),
            'address' => $emailConfig->getEmail(),
        ]);

        return $this->toEntity($model->fresh(['department']));
    }

    public function deleteById(string $id): bool
    {
        $id = trim($id);
        if ($id === '') {
            return false;
        }

        $model = EmailSettingModel::query()->find($id);
        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function findAll(array $filters = []): array
    {
        $filters = $filters ?? [];

        $sortField = $filters['sortField'] ?? 'address';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = ['id', 'address', 'department'];
        if (!in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'address';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $normalizedFilters = [
            'address' => isset($filters['address']) && is_string($filters['address']) ? trim($filters['address']) : null,
            'departmentId' => isset($filters['departmentId']) && is_string($filters['departmentId']) ? trim($filters['departmentId']) : null,
            'search' => isset($filters['search']) && is_string($filters['search']) ? trim($filters['search']) : null,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'page' => $filters['page'] ?? null,
        ];

        $query = EmailSettingModel::query()->with('department');

        if (!empty($normalizedFilters['address'])) {
            $query->where('address', 'like', '%' . $normalizedFilters['address'] . '%');
        }

        if (!empty($normalizedFilters['departmentId'])) {
            $query->where('department_id', $normalizedFilters['departmentId']);
        }

        if (!empty($normalizedFilters['search'])) {
            $search = $normalizedFilters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('address', 'like', '%' . $search . '%')
                    ->orWhereHas('department', function (Builder $departmentQuery) use ($search) {
                        $departmentQuery->where('department', 'like', '%' . $search . '%');
                    });
            });
        }

        $filtersForAppend = array_filter(
            [
                'address' => $normalizedFilters['address'],
                'departmentId' => $normalizedFilters['departmentId'],
                'search' => $normalizedFilters['search'],
                'sortField' => $normalizedFilters['sortField'],
                'sortDirection' => $normalizedFilters['sortDirection'],
            ],
            static fn($value) => !is_null($value) && $value !== ''
        );

        if ($sortField === 'department') {
            $query->leftJoin('department', 'department.id', '=', 'email_settings.department_id')
                ->orderBy('department.department', $sortDirection)
                ->select('email_settings.*');
        } else {
            $resolvedSortField = $sortField === 'address' ? 'address' : $sortField;
            $query->orderBy($resolvedSortField, $sortDirection);
        }

        $paginator = $query
            ->paginate(10)
            ->appends($filtersForAppend);

        return [
            'data' => $paginator->getCollection()->map(fn(EmailSettingModel $model) => $this->toEntity($model))->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
            'filters' => [
                'address' => $normalizedFilters['address'],
                'departmentId' => $normalizedFilters['departmentId'],
                'search' => $normalizedFilters['search'],
                'sortField' => $normalizedFilters['sortField'],
                'sortDirection' => $normalizedFilters['sortDirection'],
            ],
        ];
    }

    private function toEntity(EmailSettingModel $model): EmailConfigEntity
    {
        return new EmailConfigEntity(
            id: $model->id,
            email: (string) $model->address,
            departmentId: (string) $model->department_id,
        );
    }
}

