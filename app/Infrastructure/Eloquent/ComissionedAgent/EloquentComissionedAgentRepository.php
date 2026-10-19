<?php

namespace App\Infrastructure\Eloquent\ComissionedAgent;

use DateTimeInterface;
use App\Domain\ComissionedAgent\Entities\ComissionedAgent as ComissionedAgentEntity;
use App\Domain\ComissionedAgent\Repositories\ComissionedAgentRepositoryInterface;
use App\Models\ComissionedAgent as ComissionedAgentModel;

class EloquentComissionedAgentRepository implements ComissionedAgentRepositoryInterface
{
    public function create(ComissionedAgentEntity $comissionedAgent): ComissionedAgentEntity
    {
        $model = ComissionedAgentModel::create([
            'name'               => $comissionedAgent->getName(),
            'default_comission'  => $comissionedAgent->getDefaultComission(),
            'payment_days'       => $comissionedAgent->getPaymentDays(),
            'observations'       => $comissionedAgent->getObservations(),
            'active'             => $comissionedAgent->isActive(),
            'created_by'         => $comissionedAgent->getCreatedBy(),
            'updated_by'         => $comissionedAgent->getUpdatedBy(),
        ]);

        return $this->toEntity($model->fresh());
    }

    public function findById(string $id): ?ComissionedAgentEntity
    {
        $model = ComissionedAgentModel::query()->find($id);

        if (! $model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function update(ComissionedAgentEntity $comissionedAgent): ?ComissionedAgentEntity
    {
        $id = $comissionedAgent->getId();
        if (empty($id)) {
            return null;
        }

        $model = ComissionedAgentModel::query()->find($id);

        if (! $model) {
            return null;
        }

        $model->update([
            'name'              => $comissionedAgent->getName(),
            'default_comission' => $comissionedAgent->getDefaultComission(),
            'payment_days'      => $comissionedAgent->getPaymentDays(),
            'observations'      => $comissionedAgent->getObservations(),
            'active'            => $comissionedAgent->isActive(),
            'updated_by'        => $comissionedAgent->getUpdatedBy(),
        ]);

        return $this->toEntity($model->fresh());
    }

    public function deleteById(string $id): bool
    {
        $id = trim($id);
        if ($id === '') {
            return false;
        }

        $model = ComissionedAgentModel::query()->find($id);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function findAllForSelect(): array
    {
        return ComissionedAgentModel::query()
                ->orderBy('name', 'asc')
                ->select('id', 'name')
                ->get()
                ->toArray();
    }

    public function findAllPaginated(array $filters = []): array
    {
        $filters = $filters ?? [];

        $sortField = $filters['sortField'] ?? 'createdAt';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        $allowedSortFields = ['id', 'name', 'defaultComission', 'paymentDays', 'createdAt'];
        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'createdAt';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (! in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'desc';
        }

        $query = ComissionedAgentModel::query();

        if (! empty($filters['name']) && is_string($filters['name'])) {
            $term = trim($filters['name']);
            if ($term !== '') {
                $query->where('name', 'like', '%' . $term . '%');
            }
        }

        if (array_key_exists('defaultComission', $filters) && $filters['defaultComission'] !== null && $filters['defaultComission'] !== '') {
            $query->where('default_comission', (float) $filters['defaultComission']);
        }

        if (array_key_exists('paymentDays', $filters) && $filters['paymentDays'] !== null && $filters['paymentDays'] !== '') {
            $query->where('payment_days', (int) $filters['paymentDays']);
        }

        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : null;

        $column = match ($sortField) {
            'name' => 'name',
            'defaultComission' => 'default_comission',
            'paymentDays' => 'payment_days',
            'createdAt' => 'created_at',
            'id' => 'id',
            default => 'created_at',
        };
        $query->orderBy($column, $sortDirection);

        $paginator = $query
            ->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator->getCollection()
                ->map(fn (ComissionedAgentModel $model) => $this->toEntity($model))
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

    private function toEntity(ComissionedAgentModel $model): ComissionedAgentEntity
    {
        return new ComissionedAgentEntity(
            id: $model->id,
            name: (string) $model->name,
            defaultComission: (float) $model->default_comission,
            paymentDays: (int) $model->payment_days,
            observations: $model->observations,
            active: (bool) $model->active,
            createdBy: $model->created_by ? (int) $model->created_by : null,
            updatedBy: $model->updated_by ? (int) $model->updated_by : null,
            createdAt: $model->created_at?->format(DateTimeInterface::ATOM),
            updatedAt: $model->updated_at?->format(DateTimeInterface::ATOM),
        );
    }
}
