<?php

namespace App\Domain\Job\Entities;

class JobOrganizerLink
{
    public function __construct(
        private string $jobId,
        private string $organizerId,
        private ?string $createdAt = null,
    ) {
        $this->jobId = trim($jobId);
        $this->organizerId = trim($organizerId);

        if ($this->jobId === '') {
            throw new \InvalidArgumentException('O job é obrigatório no vínculo com o organizador.');
        }

        if ($this->organizerId === '') {
            throw new \InvalidArgumentException('O organizador é obrigatório no vínculo com o job.');
        }

        if ($this->createdAt !== null) {
            $createdAt = trim($this->createdAt);
            if ($createdAt === '') {
                $createdAt = null;
            }
            $this->createdAt = $createdAt;
        }
    }

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function getOrganizerId(): string
    {
        return $this->organizerId;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}
