<?php

namespace App\UseCase\Service\DTO;

class CreateServiceInputDto
{
    public function __construct(
        public string $service,
        public string $unityId,
        public ?string $description = null,
        public ?int $createdBy = null
    ) {}
}
