<?php

namespace App\UseCase\Service;

use App\Domain\Service\Entities\Service as ServiceEntity;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\DTO\CreateServiceInputDto;
use App\UseCase\Service\DTO\ServiceOutputDto;
use App\UseCase\UseCaseInterface;

class CreateServiceUseCase implements UseCaseInterface
{
    public function __construct(
        private ServiceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ServiceOutputDto
    {
        if (!$input instanceof CreateServiceInputDto) {
            throw new \InvalidArgumentException('Input inválido para CreateServiceUseCase');
        }

        $entity = new ServiceEntity(
            id: null,
            service: $input->service,
            description: $input->description,
            unityId: $input->unityId,
            createdBy: $input->createdBy,
            updatedBy: null
        );

        $created = $this->repository->create($entity);

        return new ServiceOutputDto(
            id: (string) $created->getId(),
            service: $created->getService(),
            description: $created->getDescription(),
            unityId: $created->getUnityId(),
            unity: $created->getUnity(),
            createdAt: $created->getCreatedAt(),
            updatedAt: $created->getUpdatedAt()
        );
    }
}
