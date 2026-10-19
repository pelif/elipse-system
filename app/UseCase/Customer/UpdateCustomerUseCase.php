<?php

namespace App\UseCase\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\UseCase\Customer\DTO\CustomerOutputDto;
use App\UseCase\Customer\DTO\UpdateCustomerInputDto;
use App\UseCase\UseCaseInterface;

class UpdateCustomerUseCase implements UseCaseInterface
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?CustomerOutputDto
    {
        if (!$input instanceof UpdateCustomerInputDto) {
            throw new \InvalidArgumentException('Input inválido para UpdateCustomerUseCase');
        }

        $entity = CustomerEntityMapper::fromUpdateDto($input);
        $updated = $this->repository->update($entity);

        if (!$updated) {
            return null;
        }

        return CustomerOutputDto::fromEntity($updated);
    }
}
