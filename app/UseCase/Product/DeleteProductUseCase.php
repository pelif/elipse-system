<?php

namespace App\UseCase\Product;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteProductUseCase implements UseCaseInterface
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para DeleteProductUseCase');
        }

        return $this->repository->deleteById($id);
    }
}
