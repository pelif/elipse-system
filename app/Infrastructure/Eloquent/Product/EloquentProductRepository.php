<?php

namespace App\Infrastructure\Eloquent\Product;

use DateTimeInterface;

use App\Domain\Product\Entities\Product as ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Models\Product as ProductModel;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function create(ProductEntity $product): ProductEntity
    {
        $model = ProductModel::create([
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ]);

        return $this->toEntity($model);
    }

    public function findById(string $id): ?ProductEntity
    {
        $model = ProductModel::query()->find($id);

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function update(ProductEntity $product): ?ProductEntity
    {
        $id = $product->getId();
        if (empty($id)) {
            return null;
        }

        $model = ProductModel::query()->find($id);

        if (!$model) {
            return null;
        }

        $model->update([
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ]);

        return $this->toEntity($model->fresh());
    }

    public function deleteById(string $id): bool
    {
        $id = trim($id);
        if ($id === '') {
            return false;
        }

        $model = ProductModel::query()->find($id);

        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function findAllPaginated(array $filters = []): array
    {
        $filters = $filters ?? [];

        $sortField = $filters['sortField'] ?? 'name';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = ['id', 'name', 'created_at', 'updated_at'];
        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'name';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (! in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $query = ProductModel::query();

        if (! empty($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : null;

        $paginator = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator->getCollection()->map(fn (ProductModel $model) => $this->toEntity($model))->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
        ];
    }

    private function toEntity(ProductModel $model): ProductEntity
    {
        return new ProductEntity(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            createdAt: $model->created_at?->format(DateTimeInterface::ATOM),
            updatedAt: $model->updated_at?->format(DateTimeInterface::ATOM),
        );
    }
}
