<?php

namespace App\UseCase\EmailConfig;

use App\Domain\EmailConfig\Repositories\EmailConfigRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteEmailConfigUseCase implements UseCaseInterface
{
    public function __construct(
        private EmailConfigRepositoryInterface $repository
    ) {}

    public function execute(mixed $input): bool
    {
        $id = (string) $input;

        return $this->repository->deleteById($id);
    }
}

