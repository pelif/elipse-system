<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Resource;

use App\UseCase\Resource\DTO\CreateResourceInputDto;
use App\UseCase\Resource\DTO\UpdateResourceInputDto;
use App\UseCase\Resource\ResourceEntityMapper;
use Tests\TestCase;

class ResourceEntityMapperTest extends TestCase
{
    public function test_from_create_dto_maps_fields_and_audit_ids(): void
    {
        $input = new CreateResourceInputDto(
            categoryId: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            departmentId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            resourceName: 'Notebook',
            patrimonialCode: 'PAT-99',
            manufacturer: 'Fab',
            model: 'M1',
            serialNumber: 'SN1',
            responsibleId: 5,
            notes: 'Obs',
            documentationLink: 'https://docs.example',
            createdBy: 100,
            updatedBy: null,
        );

        $entity = ResourceEntityMapper::fromCreateDto($input);

        $this->assertNull($entity->getId());
        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $entity->getCategoryId());
        $this->assertSame('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', $entity->getDepartmentId());
        $this->assertSame('Notebook', $entity->getResourceName());
        $this->assertSame('PAT-99', $entity->getPatrimonialCode());
        $this->assertSame('Fab', $entity->getManufacturer());
        $this->assertSame('M1', $entity->getModel());
        $this->assertSame('SN1', $entity->getSerialNumber());
        $this->assertSame(5, $entity->getResponsibleId());
        $this->assertSame('Obs', $entity->getNotes());
        $this->assertSame('https://docs.example', $entity->getDocumentationLink());
        $this->assertSame(100, $entity->getCreatedBy());
        $this->assertNull($entity->getUpdatedBy());
        $this->assertNull($entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());
    }

    public function test_from_update_dto_preserves_id_and_audit_ids(): void
    {
        $input = new UpdateResourceInputDto(
            id: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            categoryId: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            departmentId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            resourceName: 'Updated',
            patrimonialCode: 'PAT-X',
            manufacturer: null,
            model: null,
            serialNumber: null,
            responsibleId: null,
            notes: null,
            documentationLink: null,
            createdBy: 1,
            updatedBy: 200,
        );

        $entity = ResourceEntityMapper::fromUpdateDto($input);

        $this->assertSame('cccccccc-cccc-cccc-cccc-cccccccccccc', $entity->getId());
        $this->assertSame('Updated', $entity->getResourceName());
        $this->assertSame(1, $entity->getCreatedBy());
        $this->assertSame(200, $entity->getUpdatedBy());
    }
}
