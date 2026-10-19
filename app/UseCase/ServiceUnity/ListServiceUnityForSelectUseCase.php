<?php

namespace App\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\UseCaseInterface;

class ListServiceUnityForSelectUseCase implements UseCaseInterface
{
    public function __construct(
        private ServiceUnityRepositoryInterface $repository
    ) {}

    /**
     * @return array<int, array{id: string, label: string}>
     */
    public function execute(mixed $input = null): array
    {
        return $this->repository->findAllForSelect();
    }
}
