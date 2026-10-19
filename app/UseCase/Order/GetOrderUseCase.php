<?php

namespace App\UseCase\Order;

use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\UseCase\Order\DTO\OrderDetailDto;
use App\UseCase\UseCaseInterface;

class GetOrderUseCase implements UseCaseInterface
{
    public function __construct(
        private OrderRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?OrderDetailDto
    {
        $id = $this->resolveId($input);
        if ($id === '') {
            return null;
        }

        $order = $this->repository->findById($id);
        if ($order === null) {
            return null;
        }

        return OrderDetailDto::fromEntity($order);
    }

    private function resolveId(mixed $input): string
    {
        if (is_string($input)) {
            return trim($input);
        }

        if (is_array($input)) {
            $raw = null;
            if (array_key_exists('id', $input)) {
                $raw = $input['id'];
            }
            if (is_string($raw)) {
                return trim($raw);
            }
        }

        return '';
    }
}
