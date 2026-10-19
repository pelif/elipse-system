<?php

namespace App\UseCase\ResourceCategory\DTO;

class UpdateResourceCategoryInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description = null,
    ) {}
}
