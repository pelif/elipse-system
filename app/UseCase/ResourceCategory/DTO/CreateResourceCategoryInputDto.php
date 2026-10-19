<?php

namespace App\UseCase\ResourceCategory\DTO;

class CreateResourceCategoryInputDto
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
