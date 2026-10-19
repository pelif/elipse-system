<?php

namespace App\UseCase\Profile;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Models\TypeRoles;
use App\UseCase\Profile\DTO\ProfileOutputDto;
use App\UseCase\UseCaseInterface;
use Exception;

class GetProfileUseCase implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(mixed $input): ProfileOutputDto
    {
        $user = $this->repository->findById($input);

        if (!$user) {
            throw new Exception('User not found');
        }

        $role = $user->getRole();

        $roleFormatted = match ($role) {
            TypeRoles::ADMIN->value => 'Administrador',
            TypeRoles::COMMERCIAL->value => 'Comercial',
            TypeRoles::PRODUCTION->value => 'Produção',
            TypeRoles::FINANCE->value => 'Financeiro',
            default => 'Outro',
        };

        return new ProfileOutputDto(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail()->getValue(),
            role: $role,
            roleFormatted: $roleFormatted
        );
    }
}
