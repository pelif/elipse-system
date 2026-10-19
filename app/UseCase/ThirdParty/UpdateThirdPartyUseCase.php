<?php

namespace App\UseCase\ThirdParty;

use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\ThirdParty\DTO\ThirdPartyOutputDto;
use App\UseCase\ThirdParty\DTO\UpdateThirdPartyInputDto;
use App\UseCase\UseCaseInterface;

class UpdateThirdPartyUseCase implements UseCaseInterface
{
    public function __construct(
        private ThirdPartyRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ThirdPartyOutputDto
    {
        if (! $input instanceof UpdateThirdPartyInputDto) {
            throw new \InvalidArgumentException('Input inválido para UpdateThirdPartyUseCase');
        }

        $entity = ThirdPartyEntityMapper::fromUpdateDto($input);
        $updated = $this->repository->update($entity);

        if (! $updated) {
            return null;
        }

        return ThirdPartyOutputDto::fromEntity($updated);
    }
}
