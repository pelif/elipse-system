<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ThirdParty\Entities;

use App\Domain\Customer\ValueObjects\Document;
use App\Domain\ThirdParty\Entities\ThirdParty;
use Tests\TestCase;

class ThirdPartyTest extends TestCase
{
    private const VALID_CNPJ = '11222333000181';

    public function test_getters_return_constructor_values(): void
    {
        $document = Document::fromString(self::VALID_CNPJ);

        $entity = new ThirdParty(
            id: null,
            name: 'Nome Ltda',
            alias: 'Nickname',
            phone: '11999998888',
            phoneAlternative: '1133334444',
            email: 'a@example.test',
            emailAlternative: 'b@example.test',
            indication: 'Referral Partner',
            observation: 'Obs line',
            companyName: 'Razão Social SA',
            document: $document,
            active: true,
            createdBy: 1,
            createdAt: null,
            updatedBy: 2,
            updatedAt: '2026-05-04T12:00:00+00:00',
            serviceIds: ['11111111-1111-1111-1111-111111111111'],
            services: [
                [
                    'id' => '11111111-1111-1111-1111-111111111111',
                    'service' => 'Iluminação',
                ],
            ],
        );

        $this->assertNull($entity->getId());
        $this->assertSame('Nome Ltda', $entity->getName());
        $this->assertSame('Nickname', $entity->getAlias());
        $this->assertSame('11999998888', $entity->getPhone());
        $this->assertSame('1133334444', $entity->getPhoneAlternative());
        $this->assertSame('a@example.test', $entity->getEmail());
        $this->assertSame('b@example.test', $entity->getEmailAlternative());
        $this->assertSame('Referral Partner', $entity->getIndication());
        $this->assertSame('Obs line', $entity->getObservation());
        $this->assertSame('Razão Social SA', $entity->getCompanyName());
        $this->assertTrue($document->equals($entity->getDocument()));
        $this->assertTrue($entity->isActive());
        $this->assertSame(1, $entity->getCreatedBy());
        $this->assertSame(2, $entity->getUpdatedBy());
        $this->assertNull($entity->getCreatedAt());
        $this->assertSame('2026-05-04T12:00:00+00:00', $entity->getUpdatedAt());
        $this->assertSame(['11111111-1111-1111-1111-111111111111'], $entity->getServiceIds());
        $this->assertSame('Iluminação', $entity->getServices()[0]['service']);
        $this->assertNull($entity->getJobHistory());
    }

    public function test_optional_scalar_fields_can_be_null(): void
    {
        $document = Document::fromString(self::VALID_CNPJ);

        $entity = new ThirdParty(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Minimal',
            alias: null,
            phone: '',
            phoneAlternative: null,
            email: 'x@test.local',
            emailAlternative: null,
            indication: null,
            observation: null,
            companyName: null,
            document: $document,
            active: false,
            createdBy: null,
            createdAt: '2026-01-01T00:00:00+00:00',
            updatedBy: null,
            updatedAt: null,
        );

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $entity->getId());
        $this->assertNull($entity->getAlias());
        $this->assertFalse($entity->isActive());
        $this->assertSame([], $entity->getServiceIds());
        $this->assertSame([], $entity->getServices());
        $this->assertNull($entity->getJobHistory());
    }
}
