<?php

namespace App\Domain\User\Enums;

enum Roles: string
{
    case administrador = 'admin';
    case comercial = 'commercial';
    case produção = 'production';
    case financeiro = 'financial';

    public static function options(): array
    {
        return array_map(
            fn($role) => [
                'value' => $role->value,
                'label' => ucfirst($role->name)
            ],
            self::cases()
        );
    }
}
