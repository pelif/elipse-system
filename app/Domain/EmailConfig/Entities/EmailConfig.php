<?php

namespace App\Domain\EmailConfig\Entities;

class EmailConfig
{
    public function __construct(
        private ?string $id,
        private string $email,
        private string $departmentId,
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDepartmentId(): string
    {
        return $this->departmentId;
    }
}

