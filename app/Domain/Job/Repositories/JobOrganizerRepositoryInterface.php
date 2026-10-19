<?php

namespace App\Domain\Job\Repositories;

use App\Domain\Organizer\Entities\Organizer;

interface JobOrganizerRepositoryInterface
{
    /**
     * @param  array<int, string>  $organizerIds
     */
    public function syncOrganizersForJob(string $jobId, array $organizerIds): void;

    /**
     * @return array<int, string>
     */
    public function findOrganizerIdsByJobId(string $jobId): array;

    /**
     * @return array<int, Organizer>
     */
    public function findOrganizersByJobId(string $jobId): array;
}
