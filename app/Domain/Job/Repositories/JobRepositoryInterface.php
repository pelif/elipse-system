<?php

namespace App\Domain\Job\Repositories;

use App\Domain\Job\Entities\Job;

interface JobRepositoryInterface
{
    public function create(Job $job): Job;

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, Job>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;

    public function nextCodeForCustomer(string $customerId): string;
}
