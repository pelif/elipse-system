<?php

namespace App\Infrastructure\Eloquent\User\Services;

use App\Domain\User\Services\PasswordHasher;
use Illuminate\Support\Facades\Hash;

class CheckPasswordHasher implements PasswordHasher
{
    public function verify(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }
}
