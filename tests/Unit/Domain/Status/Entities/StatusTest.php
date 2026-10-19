<?php

namespace Tests\Unit\Domain\Status\Entities;

use App\Domain\Status\Entities\Status;
use App\Domain\Status\Enums\StatusType;
use Tests\TestCase;

class StatusTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $entity = new Status(
            id: null,
            status: 'Em andamento',
            type: StatusType::JOB,
            observacoes: 'Obs'
        );

        $this->assertNull($entity->getId());
        $this->assertSame('Em andamento', $entity->getStatus());
        $this->assertSame(StatusType::JOB, $entity->getType());
        $this->assertSame('Obs', $entity->getObservacoes());
    }

    public function test_observacoes_can_be_null(): void
    {
        $entity = new Status(
            id: 'uuid-1',
            status: 'Concluído',
            type: StatusType::JOB,
            observacoes: null
        );

        $this->assertSame('uuid-1', $entity->getId());
        $this->assertNull($entity->getObservacoes());
    }
}

