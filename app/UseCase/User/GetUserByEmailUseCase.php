<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\UseCase\UseCaseInterface;
use App\UseCase\User\DTO\CreatePasswordResponseDto;
use App\UseCase\User\DTO\UserOutputDto;
use Exception;

class GetUserByEmailUseCase implements UseCaseInterface
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(mixed $input): UserOutputDto
    {
        $user = $this->repository->findByEmail($input);

        if (!$user)
            throw new Exception('User not found');

        return new UserOutputDto(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail()->getValue(),
            password: $user->getPassword(),
            role: $user->getRoleName()
        );
    }
}
