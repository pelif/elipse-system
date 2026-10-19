<?php

namespace App\UseCase\ComissionedAgent;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent as ComissionedAgentEntity;
use App\Domain\ComissionedAgent\Repositories\ComissionedAgentRepositoryInterface;
use App\UseCase\ComissionedAgent\DTO\ComissionedAgentOutputDto;
use App\UseCase\ComissionedAgent\DTO\CreateComissionedAgentInputDto;
use App\UseCase\UseCaseInterface;

class CreateComissionedAgentUseCase implements UseCaseInterface
{
    public function __construct(
        private ComissionedAgentRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ComissionedAgentOutputDto
    {
        if (! $input instanceof CreateComissionedAgentInputDto) {
            throw new \InvalidArgumentException('Input inválido para CreateComissionedAgentUseCase');
        }

        $entity = new ComissionedAgentEntity(
            id: null,
            name: $input->name,
            defaultComission: $input->defaultComission,
            paymentDays: $input->paymentDays,
            observations: $input->observations,
            createdBy: $input->createdBy,
        );

        $created = $this->repository->create($entity);

        return $this->toOutputDto($created);
    }

    private function toOutputDto(ComissionedAgentEntity $entity): ComissionedAgentOutputDto
    {
        return new ComissionedAgentOutputDto(
            id: (string) $entity->getId(),
            name: $entity->getName(),
            defaultComission: $entity->getDefaultComission(),
            paymentDays: $entity->getPaymentDays(),
            observations: $entity->getObservations(),
            active: $entity->isActive(),
            createdBy: $entity->getCreatedBy(),
            updatedBy: $entity->getUpdatedBy(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
        );
    }
}
