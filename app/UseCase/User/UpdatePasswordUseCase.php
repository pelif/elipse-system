<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Models\User as UserModel;
use App\UseCase\UseCaseInterface;
use App\UseCase\User\DTO\PasswordResponseDto;
use App\UseCase\User\DTO\UserOutputDto;
use Exception;
use Illuminate\Support\Facades\Password;

class UpdatePasswordUseCase implements UseCaseInterface
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(mixed $input): PasswordResponseDto
    {
        try {
            $this->repository->updatePassword((int) $input->id, $input->password);

            // Invalidate the reset token so the creation link cannot be reused
            $userModel = UserModel::find($input->id);
            if ($userModel) {
                /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
                $broker = Password::broker();
                $broker->deleteToken($userModel);
            }

            return new PasswordResponseDto(
                success: true
            );
        } catch (\Throwable $e) {
            return new PasswordResponseDto(
                success: false,
                error: 'Não foi possível criar a senha'
            );
        }
    }
}
