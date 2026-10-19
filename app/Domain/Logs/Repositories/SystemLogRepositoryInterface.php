<?php

namespace App\Domain\Logs\Repositories;

use App\Domain\Logs\Exceptions\SystemLogFileTooLargeException;

interface SystemLogRepositoryInterface
{
    /**
     * @return array<int, array{name: string, size: int, modified_at: int|null}>
     */
    public function listFiles(): array;

    /**
     * Lê o arquivo de log completo (início ao fim).
     *
     * @return array{content: string, fileSizeBytes: int}
     *
     * @throws SystemLogFileTooLargeException quando o tamanho do arquivo excede $maxBytesAllowed
     */
    public function readEntireFile(string $file, int $maxBytesAllowed): array;
}
