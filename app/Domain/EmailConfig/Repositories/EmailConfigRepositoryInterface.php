<?php

namespace App\Domain\EmailConfig\Repositories;

use App\Domain\EmailConfig\Entities\EmailConfig;

interface EmailConfigRepositoryInterface
{
    public function create(EmailConfig $emailConfig): EmailConfig;

    public function findById(string $id): ?EmailConfig;

    public function update(EmailConfig $emailConfig): ?EmailConfig;

    public function deleteById(string $id): bool;

    /**
     * @return array{data: array<int, EmailConfig>, meta: array<string, mixed>, filters?: array<string, mixed>}
     */
    public function findAll(array $filters = []): array;
}

