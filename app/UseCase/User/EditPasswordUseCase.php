<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\UseCase\UseCaseInterface;
use App\UseCase\User\DTO\PasswordResponseDto;
use App\UseCase\User\DTO\UserOutputDto;
use Illuminate\Support\Facades\Password;

class EditPasswordUseCase implements UseCaseInterface
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(mixed $input): PasswordResponseDto
    {
        $user = $this->repository->findByEmail($input['email']);

        if (!$user) {
            return new PasswordResponseDto(
                success: false,
                error: 'Usuário não encontrado'
            );
        }

        $userModel = User::where('email', $user->getEmail()->getValue())->first();

        if (!$userModel) {
            return new PasswordResponseDto(
                success: false,
                error: 'Usuário não encontrado'
            );
        }

        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();
        $hasToken = $broker->tokenExists($userModel, $input['token']);

        if (!$hasToken) {
            return new PasswordResponseDto(
                success: false,
                error: 'O Token de Edição de Senha expirou ou é inválido.'
            );
        }

        $userDto = new UserOutputDto(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail()->getValue(),
            password: $user->getPassword(),
            role: $user->getRole()
        );

        return new PasswordResponseDto(
            success: true,
            user: $userDto,
            error: null,
            verified: $user->hasVerified(),
            hasToken: $hasToken
        );
    }
}
