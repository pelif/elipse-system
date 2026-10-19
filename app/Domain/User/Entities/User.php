<?php

namespace App\Domain\User\Entities;

use App\Domain\User\Enums\Roles;
use App\Domain\User\Services\PasswordHasher;
use App\Domain\User\ValueObjects\Email;

class User
{
    public function __construct(
        private ?string $id,
        private string $name,
        private Email $email,
        private string $password,
        private string $role,
        private ?\DateTimeInterface $verifiedAt = null,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function randPassword(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function passwordMatches(string $plainPassword, PasswordHasher $hasher): bool
    {
        return $hasher->verify($plainPassword, $this->password);
    }

    public function getRoleName(): string
    {
        $enum = Roles::from($this->role);

        if ($enum !== null)
            $this->role = $enum->name;

        return ucfirst($this->role);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getVerifiedAt(): ?\DateTimeInterface
    {
        return $this->verifiedAt;
    }

    public function findRoleValue(string $role): ?string
    {
        foreach (Roles::cases() as $enumRole) {
            if ($enumRole->name === strtolower($role)) {
                return $enumRole->value;
            }
        }
        return null;
    }

    public function hasVerified(): bool
    {
        return $this->verifiedAt !== null;
    }

    public function update(string $name, string $email, string $role): void
    {
        $this->name = $name;
        $this->email = new Email($email);
        $this->role = $this->findRoleValue($role);
    }
}
