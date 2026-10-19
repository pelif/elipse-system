<?php

namespace App\UseCase\Status;

use App\Domain\Status\Enums\StatusType;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\UseCase\Status\DTO\StatusOutputDto;
use App\UseCase\UseCaseInterface;

class GetJobStatusUseCase implements UseCaseInterface
{
    public function __construct(
        private StatusRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?StatusOutputDto
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para GetJobStatusUseCase');
        }

        $entity = $this->repository->findById($id);

        if (!$entity || $entity->getType() !== StatusType::JOB) {
            return null;
        }

        return new StatusOutputDto(
            id: (string) $entity->getId(),
            status: $entity->getStatus(),
            type: $entity->getType()->value,
            observacoes: $entity->getObservacoes()
        );
    }
}

