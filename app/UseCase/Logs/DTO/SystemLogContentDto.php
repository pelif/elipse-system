<?php

namespace App\UseCase\Logs\DTO;

readonly class SystemLogContentDto
{
    public function __construct(
        public string $file,
        public string $content,
        public int $fileSizeBytes,
        public ?string $errorMessage = null,
    ) {}
}
