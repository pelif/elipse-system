<?php

namespace App\UseCase\Status\DTO;

class UpdateJobStatusInputDto
{
    public function __construct(
        public string $id,
        public string $status,
        public ?string $observacoes = null
    ) {}
}
