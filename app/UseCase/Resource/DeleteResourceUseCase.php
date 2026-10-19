<?php

namespace App\UseCase\Resource;

use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteResourceUseCase implements UseCaseInterface
{
    public function __construct(
        private ResourceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para DeleteResourceUseCase');
        }

        return $this->repository->deleteById($id);
    }
}
