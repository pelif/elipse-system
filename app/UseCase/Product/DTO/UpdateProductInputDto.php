<?php

namespace App\UseCase\Product\DTO;

class UpdateProductInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description = null
    ) {}
}
