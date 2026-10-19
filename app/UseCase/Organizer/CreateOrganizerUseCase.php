<?php

namespace App\UseCase\Organizer;

use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\Organizer\DTO\CreateOrganizerInputDto;
use App\UseCase\Organizer\DTO\OrganizerOutputDto;
use App\UseCase\UseCaseInterface;

class CreateOrganizerUseCase implements UseCaseInterface
{
    public function __construct(
        private OrganizerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): OrganizerOutputDto
    {
        if (! $input instanceof CreateOrganizerInputDto)
            throw new \InvalidArgumentException('Input inválido para CreateOrganizerUseCase');

        $entity = OrganizerEntityMapper::fromCreateDto($input);
        $created = $this->repository->create($entity);

        return OrganizerOutputDto::fromEntity($created);
    }
}
