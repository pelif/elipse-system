<?php

namespace App\UseCase\Organizer;

use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\Organizer\DTO\OrganizerOutputDto;
use App\UseCase\Organizer\DTO\UpdateOrganizerInputDto;
use App\UseCase\UseCaseInterface;

class UpdateOrganizerUseCase implements UseCaseInterface
{
    public function __construct(
        private OrganizerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): ?OrganizerOutputDto
    {
        if (! $input instanceof UpdateOrganizerInputDto)
            throw new \InvalidArgumentException('Input inválido para UpdateOrganizerUseCase');

        $entity = OrganizerEntityMapper::fromUpdateDto($input);
        $updated = $this->repository->update($entity);

        if (! $updated) return null;

        return OrganizerOutputDto::fromEntity($updated);
    }
}
