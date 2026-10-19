<?php

namespace App\UseCase\Resource;

use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\Resource\DTO\ResourceOutputDto;
use App\UseCase\UseCaseInterface;

class ListResourcesUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllPaginated(is_array($input) ? $input : []);

        $items = array_map(
            static fn ($resource) => ResourceOutputDto::fromEntity($resource),
            $paginated['data']
        );

        return [
            'data' => $items,
            'meta' => $paginated['meta'],
            'filters' => [
                'resource_name' => $input['resource_name'] ?? null,
                'name' => $input['name'] ?? null,
                'patrimonial_code' => $input['patrimonial_code'] ?? null,
                'manufacturer' => $input['manufacturer'] ?? null,
                'model' => $input['model'] ?? null,
                'serial_number' => $input['serial_number'] ?? null,
                'category_id' => $input['category_id'] ?? null,
                'category' => $input['category'] ?? null,
                'department_id' => $input['department_id'] ?? null,
                'department' => $input['department'] ?? null,
                'responsible' => $input['responsible'] ?? null,
                'notes' => $input['notes'] ?? null,
                'documentation_link' => $input['documentation_link'] ?? null,
                'search' => $input['search'] ?? null,
                'sortField' => $input['sortField'] ?? 'resource_name',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
                'page' => $input['page'] ?? null,
                'per_page' => $input['per_page'] ?? null,
            ],
        ];
    }
}
