<?php

namespace App\Domain\ThirdParty\ValueObjects;

final class ThirdPartyJobHistory
{
    public function __construct(
        private int $jobsCount,
        private ?string $lastJobCode,
    ) {}

    public function getJobsCount(): int
    {
        return $this->jobsCount;
    }

    public function getLastJobCode(): ?string
    {
        return $this->lastJobCode;
    }

    public static function zero(): self
    {
        return new self(0, null);
    }
}
