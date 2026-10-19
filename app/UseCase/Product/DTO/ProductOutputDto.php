<?php

namespace App\UseCase\Product\DTO;

class ProductOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}
}
