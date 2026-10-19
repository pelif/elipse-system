<?php

namespace App\UseCase\Product;

use App\Domain\Product\Entities\Product as ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\Product\DTO\CreateProductInputDto;
use App\UseCase\Product\DTO\ProductOutputDto;
use App\UseCase\UseCaseInterface;

class CreateProductUseCase implements UseCaseInterface
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ProductOutputDto
    {
        if (!$input instanceof CreateProductInputDto) {
            throw new \InvalidArgumentException('Input inválido para CreateProductUseCase');
        }

        $entity = new ProductEntity(
            id: null,
            name: $input->name,
            description: $input->description
        );

        $created = $this->repository->create($entity);

        return new ProductOutputDto(
            id: (string) $created->getId(),
            name: $created->getName(),
            description: $created->getDescription(),
            createdAt: $created->getCreatedAt(),
            updatedAt: $created->getUpdatedAt()
        );
    }
}
