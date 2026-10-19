<?php

namespace App\Infrastructure\Eloquent\User;

use App\Domain\User\Entities\User as UserEntity;
use App\Models\User as UserModel;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


class EloquentUserRepository implements UserRepositoryInterface
{

    public function create(UserEntity $user): UserEntity
    {
        $model = UserModel::create([
            'name' => $user->getName(),
            'email' => $user->getEmail()->getValue(),
            'password' => Hash::make($user->randPassword()),
            'role' => $user->findRoleValue($user->getRole())
        ]);

        return $this->toEntity($model);
    }

    public function update(UserEntity $user): UserEntity
    {
        $model = UserModel::findOrFail($user->getId());
        $model->update([
            'name' => $user->getName(),
            'email' => $user->getEmail()->getValue(),
            'password' => Hash::make($user->getPassword()),
            'role' => $user->getRole()
        ]);

        return $this->toEntity($model);
    }

    public function updatePassword(int $id, string $password): UserEntity
    {
        $model = UserModel::findOrFail($id);

        $model->update([
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        return $this->toEntity($model);
    }

    public function delete(UserEntity $user): bool
    {
        return (bool) UserModel::destroy($user->getId());
    }


    public function findById(?string $id): ?UserEntity
    {
        $model = UserModel::where('id', $id)->first();

        if (!$model)
            return null;

        return $this->toEntity($model);
    }

    public function findAll(array $filters = []): array
    {
        $filters = $filters ?? [];

        $sortField = $filters['sortField'] ?? 'name';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = ['name', 'email', 'role', 'createdAt'];
        if (!in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'name';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $resolvedSortField = $sortField === 'createdAt' ? 'created_at' : $sortField;

        $normalizedFilters = [
            'name' => isset($filters['name']) && is_string($filters['name']) ? trim($filters['name']) : null,
            'email' => isset($filters['email']) && is_string($filters['email']) ? trim($filters['email']) : null,
            'role' => isset($filters['role']) && is_string($filters['role']) ? trim($filters['role']) : null,
            'createdAt' => isset($filters['createdAt']) && is_string($filters['createdAt']) ? trim($filters['createdAt']) : null,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'page' => $filters['page'] ?? null,
        ];

        $createdAtDate = null;
        if (!empty($normalizedFilters['createdAt'])) {
            try {
                $createdAtDate = Carbon::createFromFormat('d/m/Y', $normalizedFilters['createdAt'])->toDateString();
            } catch (\Throwable) {
                $createdAtDate = null;
                $normalizedFilters['createdAt'] = null;
            }
        }

        $query = UserModel::query();

        if (!empty($normalizedFilters['name'])) {
            $query->where('name', 'like', '%' . $normalizedFilters['name'] . '%');
        }

        if (!empty($normalizedFilters['email'])) {
            $query->where('email', 'like', '%' . $normalizedFilters['email'] . '%');
        }

        if (!empty($normalizedFilters['role'])) {
            $query->where('role', 'like', '%' . $normalizedFilters['role'] . '%');
        }

        if (!empty($createdAtDate)) {
            $query->whereDate('created_at', $createdAtDate);
        }

        $filtersForAppend = array_filter(
            [
                'name' => $normalizedFilters['name'],
                'email' => $normalizedFilters['email'],
                'role' => $normalizedFilters['role'],
                'createdAt' => $normalizedFilters['createdAt'],
                'sortField' => $normalizedFilters['sortField'],
                'sortDirection' => $normalizedFilters['sortDirection'],
            ],
            static fn($value) => !is_null($value) && $value !== ''
        );

        $paginator = $query
            ->orderBy($resolvedSortField, $sortDirection)
            ->paginate(10)
            ->appends($filtersForAppend);

        return [
            'data' => $paginator->getCollection()->map(fn(\App\Models\User $model) => $this->toEntity($model))->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray()
            ],
            'filters' => [
                'name' => $normalizedFilters['name'],
                'email' => $normalizedFilters['email'],
                'role' => $normalizedFilters['role'],
                'createdAt' => $normalizedFilters['createdAt'],
                'sortField' => $normalizedFilters['sortField'],
                'sortDirection' => $normalizedFilters['sortDirection'],
            ],
        ];
    }

    public function findAllForSelect(): array
    {
        $result = UserModel::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();

        return $result;
    }

    public function findByEmail(string $email): ?UserEntity
    {
        $model = UserModel::where('email', $email)->first();

        if (!$model)
            return null;

        return $this->toEntity($model);
    }

    public function findByRole(string $role): ?array
    {
        $collection = UserModel::where('role', $role)->get();

        if (!$collection)
            return null;

        return $collection->map(fn($collect) => $this->toEntity($collect))->toArray();
    }

    public function toEntity(UserModel $model): UserEntity
    {
        return new UserEntity(
            id: $model->id,
            name: $model->name,
            email: new Email($model->email),
            password: $model->password,
            role: $model->role,
            verifiedAt: $model->email_verified_at
        );
    }
}
