<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\UseCase\UseCaseInterface;
use App\UseCase\User\DTO\UserOutputDto;

use Exception;


class UpdateUserUseCase implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(mixed $input): UserOutputDto
    {
        $user = $this->repository->findById($input->id);

        if (!$user)
            throw new Exception('User not found');

        $user->update(
            $input->name,
            $input->email,
            $input->role
        );

        $updatedUser = $this->repository->update($user);

        return new UserOutputDto(
            id: $updatedUser->getId(),
            name: $updatedUser->getName(),
            email: $updatedUser->getEmail()->getValue(),
            password: $updatedUser->getPassword(),
            role: $updatedUser->getRoleName()
        );
    }
}
