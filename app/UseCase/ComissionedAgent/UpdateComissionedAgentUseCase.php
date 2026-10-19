<?php

namespace App\UseCase\ComissionedAgent;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent as ComissionedAgentEntity;
use App\Domain\ComissionedAgent\Repositories\ComissionedAgentRepositoryInterface;
use App\UseCase\ComissionedAgent\DTO\ComissionedAgentOutputDto;
use App\UseCase\ComissionedAgent\DTO\UpdateComissionedAgentInputDto;
use App\UseCase\UseCaseInterface;

class UpdateComissionedAgentUseCase implements UseCaseInterface
{
    public function __construct(
        private ComissionedAgentRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ComissionedAgentOutputDto
    {
        if (! $input instanceof UpdateComissionedAgentInputDto) {
            throw new \InvalidArgumentException('Input inválido para UpdateComissionedAgentUseCase');
        }

        $entity = new ComissionedAgentEntity(
            id: $input->id,
            name: $input->name,
            defaultComission: $input->defaultComission,
            paymentDays: $input->paymentDays,
            observations: $input->observations,
            updatedBy: $input->updatedBy,
        );

        $updated = $this->repository->update($entity);

        if (! $updated) {
            return null;
        }

        return new ComissionedAgentOutputDto(
            id: (string) $updated->getId(),
            name: $updated->getName(),
            defaultComission: $updated->getDefaultComission(),
            paymentDays: $updated->getPaymentDays(),
            observations: $updated->getObservations(),
            active: $updated->isActive(),
            createdBy: $updated->getCreatedBy(),
            updatedBy: $updated->getUpdatedBy(),
            createdAt: $updated->getCreatedAt(),
            updatedAt: $updated->getUpdatedAt(),
        );
    }
}
