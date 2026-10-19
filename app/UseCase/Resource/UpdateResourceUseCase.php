<?php

namespace App\UseCase\Resource;

use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\Resource\DTO\ResourceOutputDto;
use App\UseCase\Resource\DTO\UpdateResourceInputDto;
use App\UseCase\UseCaseInterface;

class UpdateResourceUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ResourceOutputDto
    {
        if (! $input instanceof UpdateResourceInputDto) {
            throw new \InvalidArgumentException('Input inválido para UpdateResourceUseCase');
        }

        $entity = ResourceEntityMapper::fromUpdateDto($input);
        $updated = $this->repository->update($entity);

        if (! $updated) {
            return null;
        }

        return ResourceOutputDto::fromEntity($updated);
    }
}
