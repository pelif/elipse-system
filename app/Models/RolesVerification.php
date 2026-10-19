<?php

namespace App\Models;

use App\Models\TypeRoles;

trait RolesVerification
{
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === TypeRoles::ADMIN->value;
    }

    public function isCommercial(): bool
    {
        return $this->role === TypeRoles::COMMERCIAL->value;
    }

    public function isProduction(): bool
    {
        return $this->role === TypeRoles::PRODUCTION->value;
    }

    public function isFinance(): bool
    {
        return $this->role === TypeRoles::FINANCE->value;
    }

    public function getRoleFormatted(): string
    {
        return match ($this->role) {
            TypeRoles::ADMIN->value => 'Administrador',
            TypeRoles::COMMERCIAL->value => 'Comercial',
            TypeRoles::PRODUCTION->value => 'Produção',
            TypeRoles::FINANCE->value => 'Financeiro',
            default => 'Outro',
        };
    }
}
