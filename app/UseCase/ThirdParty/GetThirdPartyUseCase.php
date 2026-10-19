<?php

namespace App\UseCase\ThirdParty;

use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\ThirdParty\DTO\ThirdPartyOutputDto;
use App\UseCase\UseCaseInterface;

class GetThirdPartyUseCase implements UseCaseInterface
{
    public function __construct(
        private ThirdPartyRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ThirdPartyOutputDto
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id)) throw new \InvalidArgumentException('Input inválido para GetThirdPartyUseCase');

        $entity = $this->repository->findById($id);

        if (! $entity) return null;

        return ThirdPartyOutputDto::fromEntity($entity);
    }
}
