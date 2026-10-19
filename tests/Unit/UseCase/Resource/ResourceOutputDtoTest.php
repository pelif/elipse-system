<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Resource;

use App\Domain\Resource\Entities\Resource;
use App\UseCase\Resource\DTO\ResourceOutputDto;
use LogicException;
use Tests\TestCase;

class ResourceOutputDtoTest extends TestCase
{
    public function test_from_entity_throws_when_id_missing(): void
    {
        $entity = new Resource(
            id: null,
            categoryId: null,
            departmentId: null,
            resourceName: 'X',
            patrimonialCode: 'Y',
            manufacturer: null,
            model: null,
            serialNumber: null,
            responsibleId: null,
            notes: null,
            documentationLink: null,
            createdAt: null,
            createdBy: null,
            updatedAt: null,
            updatedBy: null,
        );

        $this->expectException(LogicException::class);
        ResourceOutputDto::fromEntity($entity);
    }

    public function test_from_entity_maps_all_fields(): void
    {
        $entity = new Resource(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            categoryId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            departmentId: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            resourceName: 'Nome',
            patrimonialCode: 'PC',
            manufacturer: 'Mfg',
            model: 'Mdl',
            serialNumber: 'SN',
            responsibleId: 9,
            notes: 'N',
            documentationLink: 'https://d.example',
            createdAt: '2026-03-01T00:00:00+00:00',
            createdBy: 1,
            updatedAt: '2026-03-02T00:00:00+00:00',
            updatedBy: 2,
        );

        $dto = ResourceOutputDto::fromEntity($entity);

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $dto->id);
        $this->assertSame('Nome', $dto->resourceName);
        $this->assertSame(1, $dto->createdBy);
        $this->assertSame(2, $dto->updatedBy);
        $this->assertSame('2026-03-01T00:00:00+00:00', $dto->createdAt);
        $this->assertSame('2026-03-02T00:00:00+00:00', $dto->updatedAt);
    }
}
