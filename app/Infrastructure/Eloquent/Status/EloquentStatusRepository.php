<?php

namespace App\Infrastructure\Eloquent\Status;

use App\Domain\Status\Entities\Status as StatusEntity;
use App\Domain\Status\Enums\StatusType;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\Models\Status as StatusModel;

class EloquentStatusRepository implements StatusRepositoryInterface
{
    public function create(StatusEntity $status): StatusEntity
    {
        $model = StatusModel::create([
            'status' => $status->getStatus(),
            'type' => $status->getType()->value,
            'observacoes' => $status->getObservacoes(),
        ]);

        return $this->toEntity($model);
    }

    public function findById(string $id): ?StatusEntity
    {
        $model = StatusModel::query()->find($id);

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function update(StatusEntity $status): ?StatusEntity
    {
        $id = $status->getId();
        if (empty($id)) {
            return null;
        }

        $model = StatusModel::query()->find($id);

        if (!$model) {
            return null;
        }

        if ((int) $model->type !== StatusType::JOB->value) {
            return null;
        }

        $model->update([
            'status' => $status->getStatus(),
            'type' => StatusType::JOB->value,
            'observacoes' => $status->getObservacoes(),
        ]);

        return $this->toEntity($model->fresh());
    }

    public function deleteById(string $id): bool
    {
        $id = trim($id);
        if ($id === '') {
            return false;
        }

        $model = StatusModel::query()->find($id);
        if (!$model) {
            return false;
        }

        if ((int) $model->type !== StatusType::JOB->value) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function findAllByType(array $filters = []): array
    {
        $filters = $filters ?? [];


        $sortField = $filters['sortField'] ?? 'id';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = ['id', 'status', 'type', 'observacoes'];
        if (!in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'id';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $query = StatusModel::query();

        if (!empty($filters['id'])) {
            $query->where('id', 'like', '%' . $filters['id'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', 'like', '%' . $filters['status'] . '%');
        }

        if (!empty($filters['type'])) {
            $query->where('type', StatusType::valueFromLabel(strtolower($filters['type'])));
        }

        if (!empty($filters['observacoes'])) {
            $query->where('observacoes', 'like', '%' . $filters['observacoes'] . '%');
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('status', 'like', '%' . $search . '%')
                    ->orWhere('observacoes', 'like', '%' . $search . '%');
            });
        }

        $paginator = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->appends($filters);

        return [
            'data' => $paginator->getCollection()->map(fn(StatusModel $model) => $this->toEntity($model))->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
        ];
    }

    private function toEntity(StatusModel $model): StatusEntity
    {
        return new StatusEntity(
            id: $model->id,
            status: $model->status,
            type: StatusType::from((int) $model->type),
            observacoes: $model->observacoes
        );
    }
}

