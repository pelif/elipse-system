<?php

namespace App\UseCase\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteCustomerUseCase implements UseCaseInterface
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para DeleteCustomerUseCase');
        }

        return $this->repository->deleteById($id);
    }
}
