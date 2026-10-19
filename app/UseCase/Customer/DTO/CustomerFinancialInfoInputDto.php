<?php

namespace App\UseCase\Customer\DTO;

class CustomerFinancialInfoInputDto
{
    public function __construct(
        public ?int $deadlinePayment = null,
        public ?int $antecipation = null,
        public ?string $antecipationInstructions = null,
        public ?string $emailNf = null,
        public ?string $financialContactName = null,
        public ?string $financialContactPhone = null,
        public ?string $financialContactEmail = null,
    ) {}
}
