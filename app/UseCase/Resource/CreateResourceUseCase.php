<?php

namespace App\UseCase\Resource;

use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\Resource\DTO\CreateResourceInputDto;
use App\UseCase\Resource\DTO\ResourceOutputDto;
use App\UseCase\UseCaseInterface;

class CreateResourceUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ResourceOutputDto
    {
        if (! $input instanceof CreateResourceInputDto) {
            throw new \InvalidArgumentException('Input inválido para CreateResourceUseCase');
        }

        $entity = ResourceEntityMapper::fromCreateDto($input);
        $created = $this->repository->create($entity);

        return ResourceOutputDto::fromEntity($created);
    }
}
