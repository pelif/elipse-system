<?php

namespace App\Infrastructure\Eloquent\ServiceUnity;

use App\Domain\ServiceUnity\Entities\ServiceUnity;
use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\Models\ServiceUnity as ModelsServiceUnity;
use DateTimeImmutable;
use Illuminate\Support\Str;


class EloquentServiceUnityRepository implements ServiceUnityRepositoryInterface
{
    public function create(ServiceUnity $serviceUnity): ServiceUnity
    {
        $model = ModelsServiceUnity::create([
            'id' => $this->resolvId($serviceUnity->getId()),
            'unity' => $serviceUnity->getUnity(),
            'created_by' => $serviceUnity->getCreator(),
            'updated_by' => $serviceUnity->getEditor(),
        ]);

        return $this->toEntity($model);
    }

    private function resolvId(?string $id): ?string
    {
        return match (true) {
            !empty($id) => trim((string) $id),
            default => Str::uuid()
        };
    }

    public function findById(string $id): ?ServiceUnity
    {
        $model = ModelsServiceUnity::query()->find($id);

        if (!$model) return null;

        return $this->toEntity($model);
    }

    public function update(ServiceUnity $entity): ?ServiceUnity
    {
        $id = $entity->getId();

        if (empty($id)) return null;

        $model = ModelsServiceUnity::query()->find($id);

        if (!$model) return null;

        $model->update([
            'unity' => $entity->getUnity(),
            'updated_by' => $entity->getEditor()
        ]);

        return $this->toEntity($model->fresh());
    }

    public function deleteById(string $id): bool
    {
        $id = trim($id);

        if (empty($id)) return false;

        $model = ModelsServiceUnity::query()->find($id);

        if (!$model) return false;

        return (bool) $model->delete();
    }


    /**
     * @return array<int, array{id: string, label: string}>
     */
    public function findAllForSelect(): array
    {
        $rows = ModelsServiceUnity::query()
            ->orderBy('unity', 'asc')
            ->get(['id','unity']);

        $out = [];

        foreach ($rows as $row) {
            $out[] = [
                'id' => (string) $row->id,
                'label' => (string) $row->unity,
            ];
        }

        return $out;
    }


    public function findAllPaginated(array $filters = []): array
    {
        $sortField = $filters['sortField'] ?? 'unity';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = ['id', 'unity', 'created_at', 'updated_at'];

        if (!in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'unity';
        }

        $allowedSortDirections = ['asc', 'desc'];

        if (!in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $unity = $filters['unity'] ?? null;
        $createdAt = $filters['created_at'] ?? null;
        $updatedAt = $filters['updated_at'] ?? null;
        $search = $filters['search'] ?? null;

        $query = ModelsServiceUnity::query();

        if (!empty($unity)) {
            $query->where('unity', 'like', '%' . $unity . '%');
        }

        if (!empty($createdAt)) {
            $query->whereDate('created_at', $createdAt);
        }

        if (!empty($updatedAt)) {
            $query->whereDate('updated_at', $updatedAt);
        }

        if (!empty($search)) {
            $strSearch = (string) $search;
            $query->where(function ($q) use ($strSearch) {
                $q->where('unity', 'like', '%' . $strSearch . '%');

                if (strtotime($strSearch) !== false) {
                    $q->orWhereDate('created_at', $strSearch)
                        ->orWhereDate('updated_at', $strSearch);
                }
            });
        }

        $query->orderBy($sortField, $sortDirection);

        $page = null;

        if (isset($filters['page']))
            $page = max(1, (int) $filters['page']);

        $paginator = $query
            ->paginate(
                perPage: 10,
                columns: ['*'],
                pageName: 'page',
                page: $page)
            ->appends($filters);

        return [
            'data' => $paginator
                ->getCollection()
                ->map(fn (ModelsServiceUnity $model) => $this->toEntity($model))->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
        ];

    }


    private function toEntity(ModelsServiceUnity $model): ServiceUnity
    {
        $createdAt = null;
        if ($model->created_at !== null) {
            $createdAt = DateTimeImmutable::createFromMutable($model->created_at);
        }

        $updatedAt = null;
        if ($model->updated_at !== null) {
            $updatedAt = DateTimeImmutable::createFromMutable($model->updated_at);
        }

        return new ServiceUnity(
            id: $model->id,
            unity: $model->unity,
            createdBy: $model->created_by !== null ? (int) $model->created_by : null,
            updatedBy: $model->updated_by !== null ? (int) $model->updated_by : null,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }


}
