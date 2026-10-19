<?php

namespace App\UseCase\Service;

use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\DTO\ServiceOutputDto;
use App\UseCase\UseCaseInterface;

class GetServiceUseCase implements UseCaseInterface
{
    public function __construct(
        private ServiceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ServiceOutputDto
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para GetServiceUseCase');
        }

        $entity = $this->repository->findById($id);

        if (!$entity) {
            return null;
        }

        return new ServiceOutputDto(
            id: (string) $entity->getId(),
            service: $entity->getService(),
            description: $entity->getDescription(),
            unityId: $entity->getUnityId(),
            unity: $entity->getUnity(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt()
        );
    }
}
