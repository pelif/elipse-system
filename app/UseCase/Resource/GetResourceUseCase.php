<?php

namespace App\UseCase\Resource;

use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\Resource\DTO\ResourceOutputDto;
use App\UseCase\UseCaseInterface;

class GetResourceUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ResourceOutputDto
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para GetResourceUseCase');
        }

        $entity = $this->repository->findById($id);

        if (! $entity) {
            return null;
        }

        return ResourceOutputDto::fromEntity($entity);
    }
}
