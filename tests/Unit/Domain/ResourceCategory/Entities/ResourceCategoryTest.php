<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ResourceCategory\Entities;

use App\Domain\ResourceCategory\Entities\ResourceCategory;
use Tests\TestCase;

class ResourceCategoryTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $entity = new ResourceCategory(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            name: 'Equipamentos',
            description: 'Descrição da categoria',
        );

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $entity->getId());
        $this->assertSame('Equipamentos', $entity->getName());
        $this->assertSame('Descrição da categoria', $entity->getDescription());
    }

    public function test_optional_description_and_id_can_be_null(): void
    {
        $entity = new ResourceCategory(
            id: null,
            name: 'Mínimo',
            description: null,
        );

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getDescription());
        $this->assertSame('Mínimo', $entity->getName());
    }
}
