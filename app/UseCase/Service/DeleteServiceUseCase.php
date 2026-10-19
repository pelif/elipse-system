<?php

namespace App\UseCase\Service;

use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteServiceUseCase implements UseCaseInterface
{
    public function __construct(
        private ServiceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para DeleteServiceUseCase');
        }

        return $this->repository->deleteById($id);
    }
}
