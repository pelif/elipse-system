<?php

namespace App\UseCase\Logs;

use App\Domain\Logs\Repositories\SystemLogRepositoryInterface;
use App\UseCase\Logs\DTO\SystemLogFileDto;
use App\UseCase\UseCaseInterface;

class ListSystemLogsUseCase implements UseCaseInterface
{
    public function __construct(
        private SystemLogRepositoryInterface $repository
    ) {}

    /**
     * @return array{data: array<int, SystemLogFileDto>}
     */
    public function execute(mixed $input = null): array
    {
        $files = $this->repository->listFiles();

        return [
            'data' => array_map(
                static fn(array $file) => new SystemLogFileDto(
                    name: (string) ($file['name'] ?? ''),
                    size: (int) ($file['size'] ?? 0),
                    modifiedAt: isset($file['modified_at']) ? (is_int($file['modified_at']) ? $file['modified_at'] : null) : null,
                ),
                $files
            ),
        ];
    }
}

