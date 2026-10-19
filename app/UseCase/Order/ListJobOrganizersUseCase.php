<?php

namespace App\UseCase\Order;

use App\Domain\Job\Repositories\JobOrganizerRepositoryInterface;
use App\UseCase\Order\DTO\JobOrganizerOptionDto;
use App\UseCase\UseCaseInterface;

class ListJobOrganizersUseCase implements UseCaseInterface
{
    public function __construct(
        private JobOrganizerRepositoryInterface $repository
    ) {}

    /**
     * @return array<int, JobOrganizerOptionDto>
     */
    public function execute(mixed $input = null): array
    {
        $jobId = $this->resolveJobId($input);
        if ($jobId === '') {
            throw new \InvalidArgumentException('O job é obrigatório.');
        }

        $options = [];
        foreach ($this->repository->findOrganizersByJobId($jobId) as $organizer) {
            $id = $organizer->getId();
            if ($id === null || trim($id) === '') {
                continue;
            }

            $options[] = new JobOrganizerOptionDto(
                id: $id,
                name: $organizer->getName(),
                phone: $organizer->getPhone(),
                email: $organizer->getEmail(),
            );
        }

        return $options;
    }

    private function resolveJobId(mixed $input): string
    {
        if (is_string($input)) {
            return trim($input);
        }

        if (! is_array($input)) {
            return '';
        }

        $raw = null;
        if (array_key_exists('job_id', $input)) {
            $raw = $input['job_id'];
        }
        if (($raw === null || $raw === '') && array_key_exists('jobId', $input)) {
            $raw = $input['jobId'];
        }

        if (! is_string($raw)) {
            return '';
        }

        return trim($raw);
    }
}
