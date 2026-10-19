<?php

namespace App\UseCase\ThirdParty;

use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\ThirdParty\DTO\CreateThirdPartyInputDto;
use App\UseCase\ThirdParty\DTO\ThirdPartyOutputDto;
use App\UseCase\UseCaseInterface;

class CreateThirdPartyUseCase implements UseCaseInterface
{
    public function __construct(
        private ThirdPartyRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ThirdPartyOutputDto
    {
        if (! $input instanceof CreateThirdPartyInputDto) {
            throw new \InvalidArgumentException('Input inválido para CreateThirdPartyUseCase');
        }

        $entity = ThirdPartyEntityMapper::fromCreateDto($input);
        $created = $this->repository->create($entity);

        return ThirdPartyOutputDto::fromEntity($created);
    }
}
