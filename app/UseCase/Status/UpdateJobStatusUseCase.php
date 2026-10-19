<?php

namespace App\UseCase\Status;

use App\Domain\Status\Entities\Status as StatusEntity;
use App\Domain\Status\Enums\StatusType;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\UseCase\Status\DTO\StatusOutputDto;
use App\UseCase\Status\DTO\UpdateJobStatusInputDto;
use App\UseCase\UseCaseInterface;

class UpdateJobStatusUseCase implements UseCaseInterface
{
    public function __construct(
        private StatusRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?StatusOutputDto
    {
        if (!$input instanceof UpdateJobStatusInputDto) {
            throw new \InvalidArgumentException('Input inválido para UpdateJobStatusUseCase');
        }

        $existing = $this->repository->findById($input->id);

        if (!$existing || $existing->getType() !== StatusType::JOB) {
            return null;
        }

        $toSave = new StatusEntity(
            id: $input->id,
            status: $input->status,
            type: StatusType::JOB,
            observacoes: $input->observacoes
        );

        $updated = $this->repository->update($toSave);

        if (!$updated) {
            return null;
        }

        return new StatusOutputDto(
            id: (string) $updated->getId(),
            status: $updated->getStatus(),
            type: $updated->getType()->value,
            observacoes: $updated->getObservacoes()
        );
    }
}
