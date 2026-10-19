<?php

namespace App\UseCase\Contractor;

use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\UseCase\Contractor\DTO\ContractorOutputDto;
use App\UseCase\UseCaseInterface;

class GetContractorUseCase implements UseCaseInterface
{
    public function __construct(
        private ContractorRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ContractorOutputDto
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id)) throw new \InvalidArgumentException('Input inválido para GetContractorUseCase');

        $entity = $this->repository->findById($id);

        if (! $entity) return null;

        return ContractorOutputDto::fromEntity($entity);
    }
}
