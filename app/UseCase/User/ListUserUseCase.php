<?php

namespace App\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\UseCase\UseCaseInterface;
use App\UseCase\User\DTO\UserOutputDto;

class ListUserUseCase implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];
        $paginated = $this->repository->findAll($input);

        $users = array_map(fn($user) => new UserOutputDto(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail()->getValue(),
            password: $user->getPassword(),
            role: $user->getRoleName()
        ), $paginated['data']);

        return [
            'data' => $users,
            'meta' => $paginated['meta'],
            'filters' => $paginated['filters'] ?? [],
        ];
    }
}
