<?php

namespace App\UseCase\Product;

use App\Domain\Product\Entities\Product as ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\Product\DTO\ProductOutputDto;
use App\UseCase\Product\DTO\UpdateProductInputDto;
use App\UseCase\UseCaseInterface;

class UpdateProductUseCase implements UseCaseInterface
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ProductOutputDto
    {
        if (!$input instanceof UpdateProductInputDto) {
            throw new \InvalidArgumentException('Input inválido para UpdateProductUseCase');
        }

        $entity = new ProductEntity(
            id: $input->id,
            name: $input->name,
            description: $input->description
        );

        $updated = $this->repository->update($entity);

        if (!$updated) {
            return null;
        }

        return new ProductOutputDto(
            id: (string) $updated->getId(),
            name: $updated->getName(),
            description: $updated->getDescription(),
            createdAt: $updated->getCreatedAt(),
            updatedAt: $updated->getUpdatedAt()
        );
    }
}
