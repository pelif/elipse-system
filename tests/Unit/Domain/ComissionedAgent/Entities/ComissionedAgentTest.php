<?php

namespace Tests\Unit\Domain\ComissionedAgent\Entities;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent;
use Tests\TestCase;

class ComissionedAgentTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $entity = new ComissionedAgent(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Carlos Alberto',
            defaultComission: 10.5,
            paymentDays: 30,
            observations: 'Observação teste',
            active: true,
            createdBy: 1,
            updatedBy: null,
            createdAt: '2026-01-01T00:00:00+00:00',
            updatedAt: '2026-01-02T00:00:00+00:00',
        );

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $entity->getId());
        $this->assertSame('Carlos Alberto', $entity->getName());
        $this->assertSame(10.5, $entity->getDefaultComission());
        $this->assertSame(30, $entity->getPaymentDays());
        $this->assertSame('Observação teste', $entity->getObservations());
        $this->assertTrue($entity->isActive());
        $this->assertSame(1, $entity->getCreatedBy());
        $this->assertNull($entity->getUpdatedBy());
        $this->assertSame('2026-01-01T00:00:00+00:00', $entity->getCreatedAt());
        $this->assertSame('2026-01-02T00:00:00+00:00', $entity->getUpdatedAt());
    }

    public function test_id_can_be_null(): void
    {
        $entity = new ComissionedAgent(
            id: null,
            name: 'Fernanda Lima',
            defaultComission: 5.0,
            paymentDays: 15,
        );

        $this->assertNull($entity->getId());
        $this->assertSame('Fernanda Lima', $entity->getName());
        $this->assertSame(5.0, $entity->getDefaultComission());
        $this->assertSame(15, $entity->getPaymentDays());
        $this->assertNull($entity->getObservations());
        $this->assertTrue($entity->isActive());
    }

    public function test_observations_can_be_null(): void
    {
        $entity = new ComissionedAgent(
            id: null,
            name: 'João Silva',
            defaultComission: 20.0,
            paymentDays: 60,
            observations: null,
        );

        $this->assertNull($entity->getObservations());
    }

    public function test_active_defaults_to_true(): void
    {
        $entity = new ComissionedAgent(
            id: null,
            name: 'Ana Souza',
            defaultComission: 0.0,
            paymentDays: 1,
        );

        $this->assertTrue($entity->isActive());
    }
}
