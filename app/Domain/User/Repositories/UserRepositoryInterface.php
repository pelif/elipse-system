<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;

interface UserRepositoryInterface
{
    public function create(User $user): User;
    public function update(User $user): User;
    public function updatePassword(int $id, string $password): User;
    public function delete(User $user): bool;
    public function findById(string $id): ?User;
    public function findAll(array $filters = []): array;
    public function findAllForSelect(): array;
    public function findByEmail(string $email): ?User;
    public function findByRole(string $role): ?array;
}
