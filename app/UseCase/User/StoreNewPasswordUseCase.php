<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Models\User as UserModel;
use App\UseCase\UseCaseInterface;
use App\UseCase\User\DTO\PasswordResponseDto;
use Illuminate\Support\Facades\Password;

class StoreNewPasswordUseCase implements UseCaseInterface
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(mixed $input): PasswordResponseDto
    {
        $user = $this->repository->updatePassword($input['id'], $input['password']);

        if (!$user) {
            return new PasswordResponseDto(
                success: false,
                error: 'Não foi possível atualizar a senha.'
            );
        }

        // Invalidate the reset token so the link cannot be reused
        $userModel = UserModel::find($input['id']);
        if ($userModel) {
            /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
            $broker = Password::broker();
            $broker->deleteToken($userModel);
        }

        return new PasswordResponseDto(
            success: true,
            error: null
        );
    }
}
