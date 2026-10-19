<?php

namespace App\UseCase\Contractor;

use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\UseCase\Contractor\DTO\CreateContractorInputDto;
use App\UseCase\Contractor\DTO\ContractorOutputDto;
use App\UseCase\UseCaseInterface;

class CreateContractorUseCase implements UseCaseInterface
{
    public function __construct(
        private ContractorRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ContractorOutputDto
    {
        if (! $input instanceof CreateContractorInputDto)
            throw new \InvalidArgumentException('Input inválido para CreateContractorUseCase');

        $entity = ContractorEntityMapper::fromCreateDto($input);
        $created = $this->repository->create($entity);

        return ContractorOutputDto::fromEntity($created);
    }
}
