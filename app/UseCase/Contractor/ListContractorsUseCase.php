<?php

namespace App\UseCase\Contractor;

use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\UseCase\Contractor\DTO\ContractorOutputDto;
use App\UseCase\UseCaseInterface;

class ListContractorsUseCase implements UseCaseInterface
{
    public function __construct(
        private ContractorRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllPaginated(is_array($input) ? $input : []);

        $contractors = array_map(
            static fn ($contractor) => ContractorOutputDto::fromEntity($contractor),
            $paginated['data']
        );

        return [
            'data' => $contractors,
            'meta' => $paginated['meta'],
            'filters' => [
                'name' => $input['name'] ?? null,
                'phone' => $input['phone'] ?? null,
                'email' => $input['email'] ?? null,
                'customer' => $input['customer'] ?? null,
                'customerName' => $input['customerName'] ?? null,
                'company_name' => $input['company_name'] ?? null,
                'communicationCare' => $input['communicationCare'] ?? $input['communication_care'] ?? null,
                'communication_care' => $input['communication_care'] ?? null,
                'search' => $input['search'] ?? null,
                'sortField' => $input['sortField'] ?? 'name',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
            ],
        ];
    }
}
