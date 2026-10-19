<?php

namespace App\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\ResourceCategory\DTO\ResourceCategoryOutputDto;
use App\UseCase\UseCaseInterface;

class ListResourceCategoriesUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceCategoryRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllPaginated(is_array($input) ? $input : []);

        $items = array_map(
            static fn ($resourceCategory) => ResourceCategoryOutputDto::fromEntity($resourceCategory),
            $paginated['data']
        );

        return [
            'data' => $items,
            'meta' => $paginated['meta'],
            'filters' => [
                'name' => $input['name'] ?? null,
                'description' => $input['description'] ?? null,
                'search' => $input['search'] ?? null,
                'sortField' => $input['sortField'] ?? 'name',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
            ],
        ];
    }
}
