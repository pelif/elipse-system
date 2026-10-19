<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Eloquent\ResourceCategory;

use App\Domain\ResourceCategory\Entities\ResourceCategory;
use App\Infrastructure\Eloquent\ResourceCategory\EloquentResourceCategoryRepository;
use App\Models\ResourceCategory as ResourceCategoryModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentResourceCategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentResourceCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentResourceCategoryRepository;
    }

    private function makeEntity(?string $id, string $name, ?string $description = null): ResourceCategory
    {
        return new ResourceCategory(
            id: $id,
            name: $name,
            description: $description,
        );
    }

    public function test_create_assigns_uuid_when_id_null(): void
    {
        $entity = $this->makeEntity(null, 'Categoria Nova', 'Descrição inicial');

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('Categoria Nova', $created->getName());
        $this->assertSame('Descrição inicial', $created->getDescription());

        $row = ResourceCategoryModel::query()->find($created->getId());
        $this->assertNotNull($row);
        $this->assertSame('Categoria Nova', $row->name);
        $this->assertSame('Descrição inicial', $row->description);
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $found = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($found);
    }

    public function test_update_and_delete_round_trip(): void
    {
        $created = $this->repository->create($this->makeEntity(null, 'Antes', 'Desc antes'));

        $updated = $this->repository->update($this->makeEntity(
            id: $created->getId(),
            name: 'Depois',
            description: 'Desc depois',
        ));

        $this->assertNotNull($updated);
        $this->assertSame('Depois', $updated->getName());
        $this->assertSame('Desc depois', $updated->getDescription());

        $this->assertTrue($this->repository->deleteById((string) $created->getId()));
        $this->assertNull(ResourceCategoryModel::query()->find($created->getId()));
    }

    public function test_update_returns_null_when_id_blank(): void
    {
        $entity = $this->makeEntity(null, 'X', null);
        $this->assertNull($this->repository->update($entity));
    }

    public function test_find_all_paginated_filters_by_name_and_description(): void
    {
        $this->repository->create($this->makeEntity(null, 'Cat Alpha', 'notes one'));
        $this->repository->create($this->makeEntity(null, 'Cat Beta', 'notes two'));

        $byName = $this->repository->findAllPaginated(['name' => 'Alpha']);
        $this->assertSame(1, $byName['meta']['total']);
        $this->assertSame('Cat Alpha', $byName['data'][0]->getName());

        $byDesc = $this->repository->findAllPaginated(['description' => 'two']);
        $this->assertSame(1, $byDesc['meta']['total']);
        $this->assertSame('Cat Beta', $byDesc['data'][0]->getName());
    }

    public function test_find_all_paginated_search_and_second_page(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $this->repository->create($this->makeEntity(
                null,
                'Marker Cat '.$i,
                'marker desc '.$i,
            ));
        }

        $byMarker = $this->repository->findAllPaginated(['name' => 'Marker']);
        $this->assertSame(11, $byMarker['meta']['total']);
        $this->assertCount(10, $byMarker['data']);
        $this->assertSame(2, $byMarker['meta']['last_page']);
        $this->assertSame(10, $byMarker['meta']['per_page']);

        $page2 = $this->repository->findAllPaginated(['name' => 'Marker', 'page' => 2]);
        $this->assertCount(1, $page2['data']);

        $search = $this->repository->findAllPaginated(['search' => 'marker desc 3']);
        $this->assertSame(1, $search['meta']['total']);
    }

    public function test_find_all_paginated_unknown_sort_defaults_to_name(): void
    {
        $this->repository->create($this->makeEntity(null, 'Alpha Cat', null));
        $this->repository->create($this->makeEntity(null, 'Beta Cat', null));

        $result = $this->repository->findAllPaginated([
            'sortField' => 'illegal_column',
            'sortDirection' => 'desc',
        ]);

        $this->assertCount(2, $result['data']);
        $this->assertSame('Beta Cat', $result['data'][0]->getName());
        $this->assertSame('Alpha Cat', $result['data'][1]->getName());
    }

    public function test_delete_by_id_returns_false_when_row_missing(): void
    {
        $this->assertFalse($this->repository->deleteById('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa'));
    }
}
