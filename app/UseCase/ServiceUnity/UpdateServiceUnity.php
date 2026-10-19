<?php

namespace App\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\ServiceUnity\DTO\ServiceUnityInputDto;
use App\UseCase\ServiceUnity\DTO\ServiceUnityOutputDto;
use App\UseCase\UseCaseInterface;
use InvalidArgumentException;

class UpdateServiceUnity implements UseCaseInterface
{
    public function __construct(
        private ServiceUnityRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ServiceUnityOutputDto
    {
        if (!$input instanceof ServiceUnityInputDto)
            throw new InvalidArgumentException('Input Inválido para UpdateServiceUnity');

        $entity = ServiceUnityEntityMapper::fromUpdateDto($input);

        $updated = $this
            ->repository
            ->update($entity);

        if (!$updated) return null;

        return ServiceUnityOutputDto::fromEntity($updated);
    }
}
