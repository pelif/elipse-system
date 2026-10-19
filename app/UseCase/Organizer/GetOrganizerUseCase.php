<?php

namespace App\UseCase\Organizer;

use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\Organizer\DTO\OrganizerOutputDto;
use App\UseCase\UseCaseInterface;

class GetOrganizerUseCase implements UseCaseInterface
{
    public function __construct(
        private OrganizerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?OrganizerOutputDto
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id)) throw new \InvalidArgumentException('Input inválido para GetOrganizerUseCase');

        $entity = $this->repository->findById($id);

        if (! $entity) return null;

        return OrganizerOutputDto::fromEntity($entity);
    }
}
