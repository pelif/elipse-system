<?php

namespace App\UseCase\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\UseCase\Customer\DTO\CustomerOutputDto;
use App\UseCase\UseCaseInterface;

class ListCustomersUseCase implements UseCaseInterface
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllPaginated(is_array($input) ? $input : []);

        $customers = array_map(
            static fn ($customer) => CustomerOutputDto::fromEntity($customer),
            $paginated['data']
        );

        return [
            'data' => $customers,
            'meta' => $paginated['meta'],
            'filters' => [
                'companyName' => $input['companyName'] ?? $input['company_name'] ?? null,
                'legalName' => $input['legalName'] ?? $input['legal_name'] ?? null,
                'document' => $input['document'] ?? null,
                'codeCustomer' => $input['codeCustomer'] ?? $input['code_customer'] ?? null,
                'active' => $input['active'] ?? null,
                'search' => $input['search'] ?? null,
                'sortField' => $input['sortField'] ?? 'updated_at',
                'sortDirection' => $input['sortDirection'] ?? 'desc',
            ],
        ];
    }
}
