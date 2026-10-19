<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Entity\User\User;
use App\Models\User as UserModel;
use App\UseCase\UseCaseInterface;
use App\UseCase\User\DTO\PasswordResponseDto;
use Exception;
use Illuminate\Support\Facades\Password;

class NewPasswordUseCase implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(mixed $input): PasswordResponseDto
    {
        $user = $this->repository->findById($input);

        if (!$user) {
            return new PasswordResponseDto(
                success: false,
                error: 'Usuário não encontrado'
            );
        }


        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();

        $userModel = UserModel::find($user->getId());
        $token = $broker->createToken($userModel);
        $userModel->sendPasswordUpdateNotification($token);

        return new PasswordResponseDto(
            success: true
        );
    }
}
