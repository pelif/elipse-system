<?php

namespace App\Infrastructure\Files\Logs;

use App\Domain\Logs\Exceptions\SystemLogFileTooLargeException;
use App\Domain\Logs\Repositories\SystemLogRepositoryInterface;

class LocalSystemLogRepository implements SystemLogRepositoryInterface
{
    public function listFiles(): array
    {
        $paths = glob(storage_path('logs/elipse_logs_*.log')) ?: [];

        $files = array_map(static function (string $path): array {
            return [
                'name' => basename($path),
                'size' => @filesize($path) ?: 0,
                'modified_at' => @filemtime($path) ?: null,
            ];
        }, $paths);

        usort($files, static function (array $a, array $b): int {
            return ($b['modified_at'] ?? 0) <=> ($a['modified_at'] ?? 0);
        });

        return $files;
    }

    public function readEntireFile(string $file, int $maxBytesAllowed): array
    {
        $maxBytesAllowed = max(1, $maxBytesAllowed);

        $fullPathReal = $this->resolveLogPath($file);
        if ($fullPathReal === null) {
            return ['content' => '', 'fileSizeBytes' => 0];
        }

        $size = @filesize($fullPathReal);
        if (!is_int($size) || $size <= 0) {
            return ['content' => '', 'fileSizeBytes' => 0];
        }

        if ($size > $maxBytesAllowed) {
            throw new SystemLogFileTooLargeException($size, $maxBytesAllowed);
        }

        $contents = @file_get_contents($fullPathReal);

        return [
            'content' => $contents === false ? '' : $contents,
            'fileSizeBytes' => $size,
        ];
    }

    private function resolveLogPath(string $file): ?string
    {
        // Hardening against traversal even if validation happens upstream.
        if ($file !== basename($file) || str_contains($file, DIRECTORY_SEPARATOR)) {
            return null;
        }

        $logsDir = storage_path('logs');
        $fullPath = $logsDir . DIRECTORY_SEPARATOR . $file;

        $logsDirReal = realpath($logsDir);
        $fullPathReal = realpath($fullPath);
        if ($logsDirReal === false || $fullPathReal === false) {
            return null;
        }

        if (!str_starts_with($fullPathReal, $logsDirReal . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $fullPathReal;
    }
}
