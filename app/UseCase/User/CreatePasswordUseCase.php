<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\UseCase\UseCaseInterface;
use App\UseCase\User\DTO\PasswordResponseDto;
use App\UseCase\User\DTO\UserOutputDto;
use Illuminate\Support\Facades\Password;

class CreatePasswordUseCase implements UseCaseInterface
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

        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();

        $userModel = \App\Models\User::where('email', $user->getEmail()->getValue())->first();
        $hasToken = $userModel ? $broker->tokenExists($userModel, $input['token']) : false;

        // If the token is invalid and the user already has a password set,
        // show the "Senha já criada" screen.
        // If the token is invalid and the user has never set a password,
        // signal the controller to redirect to login.
        if (!$hasToken && !$user->hasVerified()) {
            return new PasswordResponseDto(
                success: false,
                error: 'O link para criação de senha expirou ou é inválido.',
                hasToken: false
            );
        }

        $userOutputDto = new UserOutputDto(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail()->getValue(),
            password: $user->getPassword(),
            role: $user->getRole()
        );

        return new PasswordResponseDto(
            success: true,
            user: $userOutputDto,
            error: null,
            verified: $user->hasVerified(),
            hasToken: $hasToken
        );
    }
}
