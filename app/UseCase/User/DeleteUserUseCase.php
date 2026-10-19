<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\UseCase\UseCaseInterface;
use Exception;

class DeleteUserUseCase implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(mixed $input): bool
    {
        $user = $this->repository->findById($input);

        if (!$user)
            throw new Exception('User not found');

        return $this->repository->delete($user);
    }
}
