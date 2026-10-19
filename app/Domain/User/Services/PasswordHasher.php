<?php

namespace App\Domain\User\Services;

interface PasswordHasher
{
    public function verify(string $password, string $hash): bool;
}
