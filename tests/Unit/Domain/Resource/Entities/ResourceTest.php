<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Resource\Entities;

use App\Domain\Resource\Entities\Resource;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $entity = new Resource(
            id: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            categoryId: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            departmentId: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            resourceName: 'Projector',
            patrimonialCode: 'PAT-001',
            manufacturer: 'Brand',
            model: 'X100',
            serialNumber: 'SN-123',
            responsibleId: 42,
            notes: 'Lab equipment',
            documentationLink: 'https://example.com/docs',
            createdAt: '2026-01-01 10:00:00',
            createdBy: 1,
            updatedAt: '2026-01-02 11:00:00',
            updatedBy: 2,
        );

        $this->assertSame('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', $entity->getId());
        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $entity->getCategoryId());
        $this->assertSame('cccccccc-cccc-cccc-cccc-cccccccccccc', $entity->getDepartmentId());
        $this->assertSame('Projector', $entity->getResourceName());
        $this->assertSame('PAT-001', $entity->getPatrimonialCode());
        $this->assertSame('Brand', $entity->getManufacturer());
        $this->assertSame('X100', $entity->getModel());
        $this->assertSame('SN-123', $entity->getSerialNumber());
        $this->assertSame(42, $entity->getResponsibleId());
        $this->assertSame('Lab equipment', $entity->getNotes());
        $this->assertSame('https://example.com/docs', $entity->getDocumentationLink());
        $this->assertSame('2026-01-01 10:00:00', $entity->getCreatedAt());
        $this->assertSame(1, $entity->getCreatedBy());
        $this->assertSame('2026-01-02 11:00:00', $entity->getUpdatedAt());
        $this->assertSame(2, $entity->getUpdatedBy());
    }

    public function test_optional_fields_can_be_null(): void
    {
        $entity = new Resource(
            id: null,
            categoryId: null,
            departmentId: null,
            resourceName: null,
            patrimonialCode: null,
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

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getCategoryId());
        $this->assertNull($entity->getDepartmentId());
        $this->assertNull($entity->getResourceName());
        $this->assertNull($entity->getPatrimonialCode());
        $this->assertNull($entity->getManufacturer());
        $this->assertNull($entity->getModel());
        $this->assertNull($entity->getSerialNumber());
        $this->assertNull($entity->getResponsibleId());
        $this->assertNull($entity->getNotes());
        $this->assertNull($entity->getDocumentationLink());
        $this->assertNull($entity->getCreatedAt());
        $this->assertNull($entity->getCreatedBy());
        $this->assertNull($entity->getUpdatedAt());
        $this->assertNull($entity->getUpdatedBy());
    }
}
