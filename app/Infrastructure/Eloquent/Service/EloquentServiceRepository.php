<?php

namespace App\Infrastructure\Eloquent\Service;

use DateTimeInterface;
use App\Domain\Service\Entities\Service as ServiceEntity;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\Models\Service as ServiceModel;

class EloquentServiceRepository implements ServiceRepositoryInterface
{
    public function create(ServiceEntity $service): ServiceEntity
    {
        $model = ServiceModel::create([
            'service'    => $service->getService(),
            'description' => $service->getDescription(),
            'unity_id'   => $service->getUnityId(),
            'created_by' => $service->getCreator(),
            'updated_by' => $service->getEditor(),
        ]);

        return $this->toEntity($model);
    }

    public function findById(string $id): ?ServiceEntity
    {
        $model = ServiceModel::query()->with('unity')->find($id);

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function update(ServiceEntity $service): ?ServiceEntity
    {
        $id = $service->getId();

        if (empty($id)) {
            return null;
        }

        $model = ServiceModel::query()->find($id);

        if (!$model) {
            return null;
        }

        $model->update([
            'service'    => $service->getService(),
            'description' => $service->getDescription(),
            'unity_id'   => $service->getUnityId(),
            'updated_by' => $service->getEditor(),
        ]);

        return $this->toEntity($model->fresh(['unity']));
    }

    public function deleteById(string $id): bool
    {
        $id = trim($id);

        if ($id === '') {
            return false;
        }

        $model = ServiceModel::query()->find($id);

        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function findAllPaginated(array $filters = []): array
    {
        $filters = $filters ?? [];

        $sortField = $filters['sortField'] ?? 'service';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = ['id', 'service', 'created_at', 'updated_at'];
        if (!in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'service';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $query = ServiceModel::query()->with('unity');

        if (!empty($filters['service'])) {
            $query->where('service', 'like', '%' . $filters['service'] . '%');
        }

        if (!empty($filters['created_at'])) {
            $query->whereDate('created_at', $filters['created_at']);
        }

        if (!empty($filters['updated_at'])) {
            $query->whereDate('updated_at', $filters['updated_at']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('service', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');

                if (strtotime($search) !== false) {
                    $q->orWhereDate('created_at', $search)
                        ->orWhereDate('updated_at', $search);
                }
            });
        }

        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : null;

        $paginator = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator
                ->getCollection()
                ->map(fn (ServiceModel $model) => $this->toEntity($model))
                ->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'links'        => $paginator->linkCollection()->toArray(),
            ],
        ];
    }

    /**
     * @return array<int, array{id: string, label: string}>
     */
    public function findAllForSelect(): array
    {
        $rows = ServiceModel::query()
            ->orderBy('service', 'asc')
            ->get(['id', 'service']);

        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'id' => (string) $row->id,
                'label' => (string) $row->service,
            ];
        }

        return $out;
    }

    private function toEntity(ServiceModel $model): ServiceEntity
    {
        $model->loadMissing('unity');

        $unityName = null;
        if ($model->unity !== null) {
            $unityName = (string) $model->unity->unity;
        }

        $createdBy = null;
        if ($model->created_by !== null) {
            $createdBy = (int) $model->created_by;
        }

        $updatedBy = null;
        if ($model->updated_by !== null) {
            $updatedBy = (int) $model->updated_by;
        }

        $createdAt = null;
        if ($model->created_at !== null) {
            $createdAt = $model->created_at->format(DateTimeInterface::ATOM);
        }

        $updatedAt = null;
        if ($model->updated_at !== null) {
            $updatedAt = $model->updated_at->format(DateTimeInterface::ATOM);
        }

        return new ServiceEntity(
            id:          $model->id,
            service:     $model->service,
            description: $model->description,
            unityId:     $model->unity_id,
            unity:       $unityName,
            createdBy:   $createdBy,
            updatedBy:   $updatedBy,
            createdAt:   $createdAt,
            updatedAt:   $updatedAt,
        );
    }
}
