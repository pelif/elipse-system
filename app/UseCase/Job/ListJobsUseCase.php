<?php

namespace App\UseCase\Job;

use App\Domain\Job\Repositories\JobRepositoryInterface;
use App\UseCase\Job\DTO\JobOutputDto;
use App\UseCase\UseCaseInterface;

class ListJobsUseCase implements UseCaseInterface
{
    public function __construct(
        private JobRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $filters = [];
        if (is_array($input)) {
            $filters = $input;
        }

        $paginated = $this->repository->findAllPaginated($filters);

        $jobs = array_map(
            static fn ($job) => JobOutputDto::fromEntity($job),
            $paginated['data']
        );

        return [
            'data' => $jobs,
            'meta' => $paginated['meta'],
            'filters' => [
                'customer' => $this->filterValue($filters, 'customer', 'customer'),
                'customerId' => $this->filterValue($filters, 'customer_id', 'customerId'),
                'jobCode' => $this->filterValue($filters, 'job_code', 'jobCode'),
                'jobTitle' => $this->filterValue($filters, 'job_title', 'jobTitle'),
                'product' => $this->filterValue($filters, 'product', 'product'),
                'productId' => $this->filterValue($filters, 'product_id', 'productId'),
                'status' => $this->filterValue($filters, 'status', 'status'),
                'statusId' => $this->filterValue($filters, 'status_id', 'statusId'),
                'contractor' => $this->filterValue($filters, 'contractor', 'contractor'),
                'contractorId' => $this->filterValue($filters, 'contractor_id', 'contractorId'),
                'createdAt' => $this->filterValue($filters, 'created_at', 'createdAt'),
                'updatedAt' => $this->filterValue($filters, 'updated_at', 'updatedAt'),
                'sortField' => $this->filterValue($filters, 'sortField', 'sort_field', 'updated_at'),
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
