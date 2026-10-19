<?php

namespace App\UseCase\Status\DTO;

class CreateJobStatusInputDto
{
    public function __construct(
        public string $status,
        public ?string $observacoes = null
    ) {}
}

