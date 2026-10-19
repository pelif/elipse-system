<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Services\PasswordHasher;
use App\Infrastructure\Eloquent\User\Services\CheckPasswordHasher;
use App\UseCase\UseCaseInterface;
use App\UseCase\User\DTO\LoginResponseDto;
use App\UseCase\User\DTO\UserOutputDto;
use Exception;

class LoginUserUseCase implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private PasswordHasher $hasher
    ) {}

    public function execute(mixed $input): LoginResponseDto
    {
        $user = $this->repository->findByEmail($input['email']);

        if (!$user) {
            return new LoginResponseDto(
                success: false,
                error: 'Usuário não encontrado'
            );
        }

        if (!$user->passwordMatches($input['password'], $this->hasher)) {
            return new LoginResponseDto(
                success: false,
                error: 'Credenciais inválidas'
            );
        }

        return new LoginResponseDto(
            success: true,
            user: $user
        );
    }
}
