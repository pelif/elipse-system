<?php

namespace App\Domain\Order\Repositories;

use App\Domain\Order\Entities\Order;

interface OrderRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, Order>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;

    public function findById(string $id): ?Order;
}
