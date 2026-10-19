<?php

namespace App\Domain\Logs\Exceptions;

use RuntimeException;

class SystemLogFileTooLargeException extends RuntimeException
{
    public function __construct(
        private readonly int $fileSizeBytes,
        private readonly int $maxAllowedBytes,
        string $message = ''
    ) {
        parent::__construct($message !== '' ? $message : sprintf(
            'O arquivo de log (%s) excede o limite permitido (%s).',
            self::formatBytes($fileSizeBytes),
            self::formatBytes($maxAllowedBytes)
        ));
    }

    public function getFileSizeBytes(): int
    {
        return $this->fileSizeBytes;
    }

    public function getMaxAllowedBytes(): int
    {
        return $this->maxAllowedBytes;
    }

    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $v = (float) $bytes;
        while ($v >= 1024 && $i < count($units) - 1) {
            $v /= 1024;
            ++$i;
        }

        return sprintf($i === 0 ? '%d %s' : '%.1f %s', $v, $units[$i]);
    }
}
