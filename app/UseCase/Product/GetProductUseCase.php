<?php

namespace App\UseCase\Product;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\Product\DTO\ProductOutputDto;
use App\UseCase\UseCaseInterface;

class GetProductUseCase implements UseCaseInterface
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ProductOutputDto
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para GetProductUseCase');
        }

        $entity = $this->repository->findById($id);

        if (!$entity) {
            return null;
        }

        return new ProductOutputDto(
            id: (string) $entity->getId(),
            name: $entity->getName(),
            description: $entity->getDescription(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt()
        );
    }
}
