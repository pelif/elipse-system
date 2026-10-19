<?php

namespace App\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\ServiceUnity\DTO\ServiceUnityOutputDto;
use App\UseCase\UseCaseInterface;

class GetServiceUnity implements UseCaseInterface
{
    public function __construct(
        private ServiceUnityRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?ServiceUnityOutputDto
    {
        $id = $this->resolveInput($input);
        $entity = $this->repository->findById($id);

        if (!$entity)
            return null;

        return new ServiceUnityOutputDto(
            id: (string) $entity->getId(),
            unity: $entity->getUnity(),
            createdBy: $entity->getCreator(),
            updatedBy: $entity->getEditor(),
            createdAt: $entity->getCreatedAt()?->format('d/m/Y H:i:s'),
            updatedAt: $entity->getUpdatedAt()?->format('d/m/Y H:i:s')
        );
    }

    private function resolveInput(mixed $input): string
    {
        $id = match(is_string($input)) {
            true => trim($input),
            false => null
        };

        return match(empty($id)) {
            true => throw new \InvalidArgumentException('Invalid Input to GetServiceUnity Usecase'),
            false => $id
        };
    }

}
