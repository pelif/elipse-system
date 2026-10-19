<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Organizer\Entities;

use App\Domain\Organizer\Entities\Organizer;
use Tests\TestCase;

class OrganizerTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $entity = new Organizer(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            customerId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            name: 'Maria Organizadora',
            phone: '11988887777',
            email: 'maria@test.local',
            communicationCare: 'Care text',
            observations: 'Note one',
            typeContact: 'Organizer',
            customerName: 'Cliente Display',
            createdBy: 1,
            createdAt: '2026-01-01T10:00:00+00:00',
            updatedBy: 2,
            updatedAt: '2026-05-04T12:00:00+00:00',
        );

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $entity->getId());
        $this->assertSame('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', $entity->getCustomerId());
        $this->assertSame('Maria Organizadora', $entity->getName());
        $this->assertSame('11988887777', $entity->getPhone());
        $this->assertSame('maria@test.local', $entity->getEmail());
        $this->assertSame('Care text', $entity->getCommunicationCare());
        $this->assertSame('Note one', $entity->getObservations());
        $this->assertSame('Organizer', $entity->getTypeContact());
        $this->assertSame('Cliente Display', $entity->getCustomerName());
        $this->assertSame(1, $entity->getCreatedBy());
        $this->assertSame(2, $entity->getUpdatedBy());
        $this->assertSame('2026-01-01T10:00:00+00:00', $entity->getCreatedAt());
        $this->assertSame('2026-05-04T12:00:00+00:00', $entity->getUpdatedAt());
    }

    public function test_optional_scalar_fields_can_be_null(): void
    {
        $entity = new Organizer(
            id: null,
            customerId: null,
            name: 'Minimal',
            phone: '',
            email: 'm@test.local',
            communicationCare: null,
            observations: null,
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
        $this->assertNull($entity->getObservations());
        $this->assertNull($entity->getTypeContact());
        $this->assertNull($entity->getCustomerName());
    }
}
