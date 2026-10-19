<?php

namespace App\UseCase\ThirdParty;

use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteThirdPartyUseCase implements UseCaseInterface
{
    public function __construct(
        private ThirdPartyRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id))
            throw new \InvalidArgumentException('Input inválido para DeleteThirdPartyUseCase');


        return $this->repository->deleteById($id);
    }
}
