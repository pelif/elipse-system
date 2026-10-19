<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Customer\Entities;

use App\Domain\Customer\Entities\ContactEntity;
use Tests\TestCase;

class ContactEntityTest extends TestCase
{
    public function test_getters_match_constructor_values(): void
    {
        $contact = new ContactEntity(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            customerId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            name: 'João Silva',
            phone: '+55 11 90000-0000',
            email: 'joao@example.com',
            typeContact: 'Operacional',
            createdBy: 1,
            updatedBy: 2,
            createdAt: '2026-01-01T00:00:00+00:00',
            updatedAt: '2026-01-02T00:00:00+00:00'
        );

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $contact->getId());
        $this->assertSame('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', $contact->getCustomerId());
        $this->assertSame('João Silva', $contact->getName());
        $this->assertSame('+55 11 90000-0000', $contact->getPhone());
        $this->assertSame('joao@example.com', $contact->getEmail());
        $this->assertSame('Operacional', $contact->getTypeContact());
        $this->assertSame(1, $contact->getCreatedBy());
        $this->assertSame(2, $contact->getUpdatedBy());
        $this->assertSame('2026-01-01T00:00:00+00:00', $contact->getCreatedAt());
        $this->assertSame('2026-01-02T00:00:00+00:00', $contact->getUpdatedAt());
    }
}
