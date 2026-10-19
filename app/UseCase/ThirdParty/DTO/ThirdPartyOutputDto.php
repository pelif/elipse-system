<?php

namespace App\UseCase\ThirdParty\DTO;

use App\Domain\ThirdParty\Entities\ThirdParty;
use App\Domain\ThirdParty\ValueObjects\ThirdPartyJobHistory;

class ThirdPartyOutputDto
{
    /**
     * @param  array<int, string>  $serviceIds
     * @param  array<int, array{id: string, service: string}>  $services
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $alias,
        public string $phone,
        public ?string $phoneAlternative,
        public string $email,
        public ?string $emailAlternative,
        public ?string $indication,
        public ?string $observation,
        public ?string $companyName,
        public string $document,
        public bool $active,
        public ?int $createdBy,
        public ?int $updatedBy,
        public ?string $createdAt,
        public ?string $updatedAt,
        public array $serviceIds,
        public array $services,
        public int $jobsCount,
        public ?string $lastJobCode,
    ) {}

    public static function fromEntity(ThirdParty $thirdParty): self
    {
        $id = $thirdParty->getId();
        if ($id === null || $id === '') {
            throw new \LogicException('ThirdParty must have an id to build ThirdPartyOutputDto.');
        }

        $jobHistory = $thirdParty->getJobHistory();
        if ($jobHistory === null) {
            $jobHistory = ThirdPartyJobHistory::zero();
        }

        return new self(
            id: $id,
            name: $thirdParty->getName(),
            alias: $thirdParty->getAlias(),
            phone: $thirdParty->getPhone(),
            phoneAlternative: $thirdParty->getPhoneAlternative(),
            email: $thirdParty->getEmail(),
            emailAlternative: $thirdParty->getEmailAlternative(),
            indication: $thirdParty->getIndication(),
            observation: $thirdParty->getObservation(),
            companyName: $thirdParty->getCompanyName(),
            document: $thirdParty->getDocument()->getValue(),
            active: $thirdParty->isActive(),
            createdBy: $thirdParty->getCreatedBy(),
            updatedBy: $thirdParty->getUpdatedBy(),
            createdAt: $thirdParty->getCreatedAt(),
            updatedAt: $thirdParty->getUpdatedAt(),
            serviceIds: $thirdParty->getServiceIds(),
            services: $thirdParty->getServices(),
            jobsCount: $jobHistory->getJobsCount(),
            lastJobCode: $jobHistory->getLastJobCode(),
        );
    }
}
