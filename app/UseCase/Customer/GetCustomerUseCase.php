<?php

namespace App\UseCase\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\UseCase\Customer\DTO\CustomerOutputDto;
use App\UseCase\UseCaseInterface;

class GetCustomerUseCase implements UseCaseInterface
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?CustomerOutputDto
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para GetCustomerUseCase');
        }

        $entity = $this->repository->findById($id);

        if (!$entity) return null;

        return CustomerOutputDto::fromEntity($entity);
    }
}
