<?php

namespace App\UseCase\Status;

use App\Domain\Status\Enums\StatusType;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteJobStatusUseCase implements UseCaseInterface
{
    public function __construct(
        private StatusRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para DeleteJobStatusUseCase');
        }

        $existing = $this->repository->findById($id);

        if (!$existing || $existing->getType() !== StatusType::JOB) {
            return false;
        }

        return $this->repository->deleteById($id);
    }
}

