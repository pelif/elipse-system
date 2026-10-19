<?php

namespace App\UseCase\Contractor;

use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteContractorUseCase implements UseCaseInterface
{
    public function __construct(
        private ContractorRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id)) throw new \InvalidArgumentException('Input inválido para DeleteContractorUseCase');

        return $this->repository->deleteById($id);
    }
}
