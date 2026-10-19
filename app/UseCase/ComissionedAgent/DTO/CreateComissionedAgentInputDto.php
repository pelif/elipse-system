<?php

namespace App\UseCase\ComissionedAgent\DTO;

class CreateComissionedAgentInputDto
{
    public function __construct(
        public string $name,
        public float $defaultComission,
        public int $paymentDays,
        public ?string $observations = null,
        public ?int $createdBy = null,
    ) {}
}
