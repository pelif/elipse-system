<?php

namespace App\UseCase\ServiceUnity\DTO;

class ServiceUnityInputDto
{
    public function __construct(
        public ?string $id,
        public string $unity,
        public ?int $createdBy=null,
        public ?int $updatedBy=null,
        public ?string $createdAt=null,
        public ?string $updatedAt=null
    ) {}
}
