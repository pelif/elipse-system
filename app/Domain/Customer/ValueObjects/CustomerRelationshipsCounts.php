<?php

namespace App\Domain\Customer\ValueObjects;

final class CustomerRelationshipsCounts
{
    public function __construct(
        private int $jobs,
        private int $contractors,
        private int $organizers,
    ) {}

    public function getJobs(): int
    {
        return $this->jobs;
    }

    public function getContractors(): int
    {
        return $this->contractors;
    }

    public function getOrganizers(): int
    {
        return $this->organizers;
    }

    public static function zero(): self
    {
        return new self(0,0,0);
    }
}
