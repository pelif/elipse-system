<?php

namespace App\UseCase\Logs;

use App\Domain\Logs\Exceptions\SystemLogFileTooLargeException;
use App\Domain\Logs\Repositories\SystemLogRepositoryInterface;
use App\UseCase\Logs\DTO\SystemLogContentDto;
use App\UseCase\UseCaseInterface;

class GetSystemLogContentUseCase implements UseCaseInterface
{
    public function __construct(
        private SystemLogRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): SystemLogContentDto
    {
        if (!is_array($input)) {
            throw new \InvalidArgumentException('Input inválido para GetSystemLogContentUseCase');
        }

        if (!isset($input['file']) || !is_string($input['file'])) {
            throw new \InvalidArgumentException('Parâmetro file é obrigatório');
        }

        $file = trim($input['file']);

        if ($file === '') {
            throw new \InvalidArgumentException('Parâmetro file é obrigatório');
        }

        $maxBytes = (int) config('system_logs.max_file_bytes', 48 * 1024 * 1024);
        $maxBytes = max(1_000, $maxBytes);

        try {
            $result = $this->repository->readEntireFile($file, $maxBytes);
        } catch (SystemLogFileTooLargeException $e) {
            return new SystemLogContentDto(
                file: $file,
                content: '',
                fileSizeBytes: $e->getFileSizeBytes(),
                errorMessage: $e->getMessage(),
            );
        }

        $originalContent = (string) ($result['content'] ?? '');
        $reversedContent = $this->reverseLines($originalContent);

        return new SystemLogContentDto(
            file: $file,
            content: $reversedContent,
            fileSizeBytes: (int) ($result['fileSizeBytes'] ?? 0),
            errorMessage: null,
        );
    }

    private function reverseLines(string $content): string
    {
        if ($content === '') {
            return '';
        }

        $lines = preg_split('/\R/u', $content);
        if ($lines === false || $lines === []) {
            return $content;
        }

        $lines = array_reverse($lines);

        return implode(PHP_EOL, $lines);
    }
}
