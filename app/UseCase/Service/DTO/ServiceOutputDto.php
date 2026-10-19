<?php

namespace App\UseCase\Service\DTO;

class ServiceOutputDto
{
    public function __construct(
        public string $id,
        public string $service,
        public ?string $description = null,
            public ?string $unityId = null,
            public ?string $unity = null,
            public ?string $createdAt = null,
            public ?string $updatedAt = null
    ) {}
}
