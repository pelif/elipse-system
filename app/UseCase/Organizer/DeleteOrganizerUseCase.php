<?php

namespace App\UseCase\Organizer;

use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteOrganizerUseCase implements UseCaseInterface
{
    public function __construct(
        private OrganizerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = match (true) {
            is_string($input) => trim($input),
            default => null,
        };

        if (empty($id)) throw new \InvalidArgumentException('Input inválido para DeleteOrganizerUseCase');

        return $this->repository->deleteById($id);
    }
}
