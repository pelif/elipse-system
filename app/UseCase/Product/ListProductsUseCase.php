<?php

namespace App\UseCase\Product;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\Product\DTO\ProductOutputDto;
use App\UseCase\UseCaseInterface;

class ListProductsUseCase implements UseCaseInterface
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllPaginated(is_array($input) ? $input : []);

        $products = array_map(
            fn ($product) => new ProductOutputDto(
                id: (string) $product->getId(),
                name: $product->getName(),
                description: $product->getDescription(),
                createdAt: $product->getCreatedAt(),
                updatedAt: $product->getUpdatedAt()
            ),
            $paginated['data']
        );

        return [
            'data' => $products,
            'meta' => $paginated['meta'],
            'filters' => [
                'name' => $input['name'] ?? null,
                'search' => $input['search'] ?? null,
                'sortField' => $input['sortField'] ?? 'name',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
            ],
        ];
    }

}
