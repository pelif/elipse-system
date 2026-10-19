<?php

namespace App\UseCase\Contractor;

use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\UseCase\Contractor\DTO\ContractorOutputDto;
use App\UseCase\Contractor\DTO\UpdateContractorInputDto;
use App\UseCase\UseCaseInterface;

class UpdateContractorUseCase implements UseCaseInterface
{
    public function __construct(
        private ContractorRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ContractorOutputDto
    {
        if (! $input instanceof UpdateContractorInputDto)
            throw new \InvalidArgumentException('Input inválido para UpdateContractorUseCase');

        $entity = ContractorEntityMapper::fromUpdateDto($input);
        $updated = $this->repository->update($entity);

        if (! $updated) return null;

        return ContractorOutputDto::fromEntity($updated);
    }
}
