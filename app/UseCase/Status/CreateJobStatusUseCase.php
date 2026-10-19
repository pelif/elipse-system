<?php

namespace App\UseCase\Status;

use App\Domain\Status\Entities\Status as StatusEntity;
use App\Domain\Status\Enums\StatusType;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\UseCase\Status\DTO\CreateJobStatusInputDto;
use App\UseCase\Status\DTO\StatusOutputDto;
use App\UseCase\UseCaseInterface;

class CreateJobStatusUseCase implements UseCaseInterface
{
    public function __construct(
        private StatusRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): StatusOutputDto
    {
        if (!$input instanceof CreateJobStatusInputDto) {
            throw new \InvalidArgumentException('Input inválido para CreateJobStatusUseCase');
        }

        $entity = new StatusEntity(
            id: null,
            status: $input->status,
            type: StatusType::JOB,
            observacoes: $input->observacoes
        );

        $created = $this->repository->create($entity);

        return new StatusOutputDto(
            id: (string) $created->getId(),
            status: $created->getStatus(),
            type: $created->getType()->value,
            observacoes: $created->getObservacoes()
        );
    }
}

