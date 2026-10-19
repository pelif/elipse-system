<?php

namespace App\UseCase\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\UseCase\User\DTO\UserOutputDto;
use App\UseCase\UseCaseInterface;

class CreateUserUseCase implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(mixed $input): UserOutputDto
    {
        $user = new User(
            id: null,
            name: $input->name,
            email: new Email($input->email),
            password: $input->password,
            role: $input->role
        );

        $createdUser = $this->repository->create($user);

        return new UserOutputDto(
            id: $createdUser->getId(),
            name: $createdUser->getName(),
            email: $createdUser->getEmail()->getValue(),
            password: $createdUser->getPassword(),
            role: $createdUser->getRoleName()
        );
    }
}
