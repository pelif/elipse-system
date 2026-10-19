<?php

namespace App\UseCase\ComissionedAgent;

use App\Domain\ComissionedAgent\Repositories\ComissionedAgentRepositoryInterface;
use App\UseCase\ComissionedAgent\DTO\ComissionedAgentOutputDto;
use App\UseCase\UseCaseInterface;

class GetComissionedAgentUseCase implements UseCaseInterface
{
    public function __construct(
        private ComissionedAgentRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ComissionedAgentOutputDto
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para GetComissionedAgentUseCase');
        }

        $entity = $this->repository->findById($id);

        if (! $entity) {
            return null;
        }

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
