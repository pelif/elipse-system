<?php

namespace Tests\Unit\Domain\Product\Entities;

use App\Domain\Product\Entities\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $entity = new Product(
            id: null,
            name: 'Notebook',
            description: 'Desc',
            createdAt: '2026-01-01T00:00:00+00:00',
            updatedAt: '2026-01-02T00:00:00+00:00'
        );

        $this->assertNull($entity->getId());
        $this->assertSame('Notebook', $entity->getName());
        $this->assertSame('Desc', $entity->getDescription());
        $this->assertSame('2026-01-01T00:00:00+00:00', $entity->getCreatedAt());
        $this->assertSame('2026-01-02T00:00:00+00:00', $entity->getUpdatedAt());
    }

    public function test_description_can_be_null(): void
    {
        $entity = new Product(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Mouse',
            description: null
        );

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $entity->getId());
        $this->assertNull($entity->getDescription());
    }
}
