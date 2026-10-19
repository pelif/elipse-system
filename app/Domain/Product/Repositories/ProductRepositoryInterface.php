<?php

namespace App\Domain\Product\Repositories;

use App\Domain\Product\Entities\Product;

interface ProductRepositoryInterface
{
    public function create(Product $product): Product;

    public function findById(string $id): ?Product;

    public function update(Product $product): ?Product;

    public function deleteById(string $id): bool;

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, Product>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;
}
