<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Contractor\Entities;

use App\Domain\Contractor\Entities\Contractor;
use Tests\TestCase;

class ContractorTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $entity = new Contractor(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            customerId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            name: 'João Contratante',
            phone: '11988887777',
            email: 'joao@test.local',
            communicationCare: 'Care text',
            typeContact: 'Contractor',
            customerName: 'Cliente Display',
            createdBy: 1,
            createdAt: '2026-01-01T10:00:00+00:00',
            updatedBy: 2,
            updatedAt: '2026-05-04T12:00:00+00:00',
        );

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $entity->getId());
        $this->assertSame('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', $entity->getCustomerId());
        $this->assertSame('João Contratante', $entity->getName());
        $this->assertSame('11988887777', $entity->getPhone());
        $this->assertSame('joao@test.local', $entity->getEmail());
        $this->assertSame('Care text', $entity->getCommunicationCare());
        $this->assertSame('Contractor', $entity->getTypeContact());
        $this->assertSame('Cliente Display', $entity->getCustomerName());
        $this->assertSame(1, $entity->getCreatedBy());
        $this->assertSame(2, $entity->getUpdatedBy());
        $this->assertSame('2026-01-01T10:00:00+00:00', $entity->getCreatedAt());
        $this->assertSame('2026-05-04T12:00:00+00:00', $entity->getUpdatedAt());
    }

    public function test_optional_scalar_fields_can_be_null(): void
    {
        $entity = new Contractor(
            id: null,
            customerId: null,
            name: 'Minimal',
            phone: '',
            email: 'm@test.local',
            communicationCare: null,
            typeContact: null,
            customerName: null,
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getCustomerId());
        $this->assertNull($entity->getCommunicationCare());
        $this->assertNull($entity->getTypeContact());
        $this->assertNull($entity->getCustomerName());
    }
}
