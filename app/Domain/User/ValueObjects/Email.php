<?php

namespace App\Domain\User\ValueObjects;

class Email
{
    public function __construct(private string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }
    }

    public function getValue(): string
    {
        return $this->email;
    }
}
