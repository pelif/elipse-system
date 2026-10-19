<?php

namespace App\UseCase\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\UseCase\Customer\DTO\CreateCustomerInputDto;
use App\UseCase\Customer\DTO\CustomerOutputDto;
use App\UseCase\UseCaseInterface;

class CreateCustomerUseCase implements UseCaseInterface
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): CustomerOutputDto
    {
        if (!$input instanceof CreateCustomerInputDto) {
            throw new \InvalidArgumentException('Input inválido para CreateCustomerUseCase');
        }

        $entity = CustomerEntityMapper::fromCreateDto($input);
        $created = $this->repository->create($entity);

        return CustomerOutputDto::fromEntity($created);
    }
}
