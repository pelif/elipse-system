<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ServiceUnity\Entities;

use App\Domain\ServiceUnity\Entities\ServiceUnity;
use DateTimeImmutable;
use Illuminate\Support\Str;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ServiceUnityTest extends TestCase
{

    public function test_getters_return_constructor_values(): void
    {
        $uuid = Str::uuid()->toString();
        $createdBy = 1;
        $updatedBy = 2;

        $entity = new ServiceUnity(
            id: $uuid,
            unity: 'Câmera Man',
            createdBy: $createdBy,
            updatedBy: $updatedBy,
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable()
        );

        $this->assertSame($uuid, $entity->getId());
        $this->assertSame('Câmera Man', $entity->getUnity());
        $this->assertSame($createdBy, $entity->getCreator());
        $this->assertSame($updatedBy, $entity->getEditor());
        $this->assertInstanceOf(DateTimeImmutable::class, $entity->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $entity->getUpdatedAt());
    }

    public function test_if_attrs_is_null(): void
    {
        $entity = new ServiceUnity(
            id: null,
            unity: 'Unity of Service 001',
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        );

        $this->assertNull($entity->getId());
        $this->assertNotNull($entity->getUnity());
        $this->assertNull($entity->getCreator());
        $this->assertNull($entity->getEditor());
        $this->assertNull($entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());
    }

    public function test_new_entity_before_persistence_has_only_required_fields(): void
    {
        $entity = new ServiceUnity(
            id: null,
            unity: 'Diária',
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        );
        $this->assertNull($entity->getId());
        $this->assertSame('Diária', $entity->getUnity());
        $this->assertNull($entity->getCreator());
        $this->assertNull($entity->getEditor());
    }

    public function test_persisted_entity_can_have_creator_without_editor(): void
    {
        $uuid = Str::uuid()->toString();
        $createdBy = 10;
        $createdAt = new DateTimeImmutable('2026-09-03 10:00:00');
        $entity = new ServiceUnity(
            id: $uuid,
            unity: 'Hora',
            createdBy: $createdBy,
            updatedBy: null,
            createdAt: $createdAt,
            updatedAt: null
        );
        $this->assertSame($uuid, $entity->getId());
        $this->assertSame($createdBy, $entity->getCreator());
        $this->assertNull($entity->getEditor());
        $this->assertSame($createdAt, $entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());
    }

    public function test_created_and_updated_dates_can_be_different(): void
    {
        $createdAt = new DateTimeImmutable('2026-09-03 10:00:00');
        $updatedAt = new DateTimeImmutable('2026-09-03 15:30:00');
        $entity = new ServiceUnity(
            id: Str::uuid()->toString(),
            unity: 'Pacote',
            createdBy: 1,
            updatedBy: 2,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
        $this->assertNotSame($entity->getCreatedAt(), $entity->getUpdatedAt());
        $this->assertSame($createdAt, $entity->getCreatedAt());
        $this->assertSame($updatedAt, $entity->getUpdatedAt());
    }

    #[DataProvider('unityNameProvider')]
    public function test_unity_accepts_different_string_formats(string $unity): void
    {
        $entity = new ServiceUnity(
            id: null,
            unity: $unity,
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        );
        $this->assertSame($unity, $entity->getUnity());
    }

    public static function unityNameProvider(): array
    {
        return [
            'with accent' => ['Câmera Man'],
            'with spaces' => ['  Unidade com espaços  '],
            'numeric label' => ['001'],
            'long name' => [str_repeat('A', 120)],
        ];
    }
}
