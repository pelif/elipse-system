<?php

namespace App\UseCase\Order;

use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\UseCase\Order\DTO\OrderListItemDto;
use App\UseCase\UseCaseInterface;

class ListOrdersUseCase implements UseCaseInterface
{
    public function __construct(
        private OrderRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $filters = [];
        if (is_array($input)) {
            $filters = $input;
        }

        $paginated = $this->repository->findAllPaginated($filters);

        $orders = array_map(
            static fn ($order) => OrderListItemDto::fromEntity($order),
            $paginated['data']
        );

        return [
            'data' => $orders,
            'meta' => $paginated['meta'],
            'filters' => [
                'osCode' => $this->filterValue($filters, 'order_code', 'osCode', $this->filterValue($filters, 'os_code', 'osCode')),
                'jobCode' => $this->filterValue($filters, 'job_code', 'jobCode'),
                'eventTitle' => $this->filterValue($filters, 'event_title', 'eventTitle'),
                'customer' => $this->filterValue($filters, 'customer', 'customer'),
                'organizer' => $this->filterValue($filters, 'organizer', 'organizerName'),
                'sortField' => $this->filterValue($filters, 'sortField', 'sort_field', 'created_at'),
                'sortDirection' => $this->filterValue($filters, 'sortDirection', 'sort_direction', 'desc'),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filterValue(array $filters, string $snake, string $camel, mixed $default = null): mixed
    {
        if (array_key_exists($snake, $filters) && $filters[$snake] !== null && $filters[$snake] !== '') {
            return $filters[$snake];
        }

        if (array_key_exists($camel, $filters) && $filters[$camel] !== null && $filters[$camel] !== '') {
            return $filters[$camel];
        }

        return $default;
    }
}
