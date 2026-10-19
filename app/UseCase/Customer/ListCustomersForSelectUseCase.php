<?php

namespace App\UseCase\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\UseCase\UseCaseInterface;

class ListCustomersForSelectUseCase implements UseCaseInterface
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    /**
     * @return array<int, array{id: string, company_name: string|null}>
     */
    public function execute(mixed $input = null): array
    {
        return $this->repository->findAllForSelect();
    }
}
