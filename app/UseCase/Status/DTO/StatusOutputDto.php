<?php

namespace App\UseCase\Status\DTO;

class StatusOutputDto
{
    public function __construct(
        public string $id,
        public string $status,
        public int $type,
        public ?string $observacoes = null
    ) {}
}

