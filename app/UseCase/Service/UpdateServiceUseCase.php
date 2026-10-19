<?php

namespace App\UseCase\Service;

use App\Domain\Service\Entities\Service as ServiceEntity;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\DTO\ServiceOutputDto;
use App\UseCase\Service\DTO\UpdateServiceInputDto;
use App\UseCase\UseCaseInterface;

class UpdateServiceUseCase implements UseCaseInterface
{
    public function __construct(
        private ServiceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ServiceOutputDto
    {
        if (!$input instanceof UpdateServiceInputDto) {
            throw new \InvalidArgumentException('Input inválido para UpdateServiceUseCase');
        }

        $entity = new ServiceEntity(
            id: $input->id,
            service: $input->service,
            description: $input->description,
            unityId: $input->unityId,
            createdBy: null,
            updatedBy: $input->updatedBy
        );

        $updated = $this->repository->update($entity);

        if (!$updated) {
            return null;
        }

        return new ServiceOutputDto(
            id: (string) $updated->getId(),
            service: $updated->getService(),
            description: $updated->getDescription(),
            unityId: $updated->getUnityId(),
            unity: $updated->getUnity(),
            createdAt: $updated->getCreatedAt(),
            updatedAt: $updated->getUpdatedAt()
        );
    }
}
