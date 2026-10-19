<?php

namespace App\Domain\Status\Entities;

use App\Domain\Status\Enums\StatusType;

class Status
{
    public function __construct(
        private ?string $id,
        private string $status,
        private StatusType $type,
        private ?string $observacoes = null
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getType(): StatusType
    {
        return $this->type;
    }

    public function getObservacoes(): ?string
    {
        return $this->observacoes;
    }
}

