<?php

namespace Tests\Unit\Domain\Service\Entities;

use App\Domain\Service\Entities\Service;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $entity = new Service(
            id: '550e8400-e29b-41d4-a716-446655440000',
            service: 'Manutenção',
            description: 'Descrição do serviço',
            unityId: 'aaaa-bbbb-cccc-dddd',
            unity: 'Hora',
            createdBy: 1,
            updatedBy: 2,
            createdAt: '2026-01-01T00:00:00+00:00',
            updatedAt: '2026-01-02T00:00:00+00:00'
        );

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $entity->getId());
        $this->assertSame('Manutenção', $entity->getService());
        $this->assertSame('Descrição do serviço', $entity->getDescription());
        $this->assertSame('aaaa-bbbb-cccc-dddd', $entity->getUnityId());
        $this->assertSame('Hora', $entity->getUnity());
        $this->assertSame(1, $entity->getCreator());
        $this->assertSame(2, $entity->getEditor());
        $this->assertSame('2026-01-01T00:00:00+00:00', $entity->getCreatedAt());
        $this->assertSame('2026-01-02T00:00:00+00:00', $entity->getUpdatedAt());
    }

    public function test_description_can_be_null(): void
    {
        $entity = new Service(
            id: null,
            service: 'Instalação',
            description: null
        );

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getDescription());
    }

    public function test_unity_id_can_be_null(): void
    {
        $entity = new Service(
            id: '550e8400-e29b-41d4-a716-446655440000',
            service: 'Suporte',
            unityId: null
        );

        $this->assertNull($entity->getUnityId());
        $this->assertNull($entity->getUnity());
    }

    public function test_uuid_is_preserved(): void
    {
        $uuid = '11111111-2222-3333-4444-555555555555';

        $entity = new Service(
            id: $uuid,
            service: 'Consultoria'
        );

        $this->assertSame($uuid, $entity->getId());
    }
}
