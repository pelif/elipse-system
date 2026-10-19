<?php

namespace App\UseCase\ComissionedAgent\DTO;

class UpdateComissionedAgentInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public float $defaultComission,
        public int $paymentDays,
        public ?string $observations = null,
        public ?int $updatedBy = null,
    ) {}
}
