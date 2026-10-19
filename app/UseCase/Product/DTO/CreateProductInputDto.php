<?php

namespace App\UseCase\Product\DTO;

class CreateProductInputDto
{
    public function __construct(
        public string $name,
        public ?string $description = null
    ) {}
}
