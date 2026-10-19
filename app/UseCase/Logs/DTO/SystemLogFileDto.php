<?php

namespace App\UseCase\Logs\DTO;

readonly class SystemLogFileDto
{
    public function __construct(
        public string $name,
        public int $size,
        public ?int $modifiedAt
    ) {}
}

