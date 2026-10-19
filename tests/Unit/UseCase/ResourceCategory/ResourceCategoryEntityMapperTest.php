<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ResourceCategory;

use App\UseCase\ResourceCategory\DTO\CreateResourceCategoryInputDto;
use App\UseCase\ResourceCategory\DTO\UpdateResourceCategoryInputDto;
use App\UseCase\ResourceCategory\ResourceCategoryEntityMapper;
use Tests\TestCase;

class ResourceCategoryEntityMapperTest extends TestCase
{
    public function test_from_create_dto_builds_entity(): void
    {
        $input = new CreateResourceCategoryInputDto(
            name: 'Nome mapeado',
            description: 'Texto',
        );

        $entity = ResourceCategoryEntityMapper::fromCreateDto($input);

        $this->assertNull($entity->getId());
        $this->assertSame('Nome mapeado', $entity->getName());
        $this->assertSame('Texto', $entity->getDescription());
    }

    public function test_from_update_dto_preserves_id_and_maps_fields(): void
    {
        $input = new UpdateResourceCategoryInputDto(
            id: 'dddddddd-dddd-dddd-dddd-dddddddddddd',
            name: 'Atualizado',
            description: null,
        );

        $entity = ResourceCategoryEntityMapper::fromUpdateDto($input);

        $this->assertSame('dddddddd-dddd-dddd-dddd-dddddddddddd', $entity->getId());
        $this->assertSame('Atualizado', $entity->getName());
        $this->assertNull($entity->getDescription());
    }
}
