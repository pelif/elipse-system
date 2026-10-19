<?php

namespace App\UseCase\Service\DTO;

class UpdateServiceInputDto
{
    public function __construct(
        public string $id,
        public string $service,
        public string $unityId,
        public ?string $description = null,
        public ?int $updatedBy = null
    ) {}
}
