<?php

namespace App\UseCase\Service;

use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\UseCaseInterface;

class ListServicesForSelectUseCase implements UseCaseInterface
{
    public function __construct(
        private ServiceRepositoryInterface $repository
    ) {}

    /**
     * @return array<int, array{id: string, label: string}>
     */
    public function execute(mixed $input = null): array
    {
        return $this->repository->findAllForSelect();
    }
}
