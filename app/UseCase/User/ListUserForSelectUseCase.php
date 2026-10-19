<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\UseCase\UseCaseInterface;

class ListUserForSelectUseCase implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        return $this->repository->findAllForSelect();
    }

}
