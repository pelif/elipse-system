<?php

namespace App\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\ServiceUnity\DTO\ServiceUnityInputDto;
use App\UseCase\ServiceUnity\DTO\ServiceUnityOutputDto;
use App\UseCase\UseCaseInterface;
use InvalidArgumentException;

class CreateServiceUnity implements UseCaseInterface
{
    public function __construct(
        private ServiceUnityRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ServiceUnityOutputDto
    {
        if (!$input instanceof ServiceUnityInputDto)
            throw new InvalidArgumentException('Invalid input to CreateServiceUnity Usecase');

        $entity = ServiceUnityEntityMapper::fromCreateDto($input);
        $created = $this->repository->create($entity);

        return ServiceUnityOutputDto::fromEntity($created);
    }
}
