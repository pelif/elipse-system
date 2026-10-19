<?php

namespace Tests\Integration\Infrastructure\Eloquent\Product;

use App\Domain\Product\Entities\Product as ProductEntity;
use App\Infrastructure\Eloquent\Product\EloquentProductRepository;
use App\Models\Product as ProductModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentProductRepository();
    }

    public function test_create_product(): void
    {
        $entity = new ProductEntity(
            id: null,
            name: 'Produto A',
            description: 'Detalhes'
        );

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('Produto A', $created->getName());
        $this->assertSame('Detalhes', $created->getDescription());
    }

    public function test_find_by_id_returns_entity(): void
    {
        $created = $this->repository->create(new ProductEntity(
            id: null,
            name: 'Buscar',
            description: null
        ));

        $found = $this->repository->findById((string) $created->getId());

        $this->assertNotNull($found);
        $this->assertSame($created->getId(), $found->getId());
        $this->assertSame('Buscar', $found->getName());
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $found = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($found);
    }

    public function test_update_persists_fields(): void
    {
        $created = $this->repository->create(new ProductEntity(
            id: null,
            name: 'Antes',
            description: 'A'
        ));

        $updated = $this->repository->update(new ProductEntity(
            id: (string) $created->getId(),
            name: 'Depois',
            description: 'B'
        ));

        $this->assertNotNull($updated);
        $this->assertSame('Depois', $updated->getName());
        $this->assertSame('B', $updated->getDescription());

        $fromDb = ProductModel::query()->find($created->getId());
        $this->assertSame('Depois', $fromDb->name);
    }

    public function test_delete_by_id(): void
    {
        $created = $this->repository->create(new ProductEntity(
            id: null,
            name: 'Remover',
            description: null
        ));

        $deleted = $this->repository->deleteById((string) $created->getId());

        $this->assertTrue($deleted);
        $this->assertNull(ProductModel::query()->find($created->getId()));
    }

    public function test_find_all_paginated_with_search(): void
    {
        $this->repository->create(new ProductEntity(id: null, name: 'Alpha X', description: null));
        $this->repository->create(new ProductEntity(id: null, name: 'Beta Y', description: null));

        $result = $this->repository->findAllPaginated(['search' => 'Alpha']);

        $this->assertCount(1, $result['data']);
        $this->assertSame('Alpha X', $result['data'][0]->getName());
        $this->assertArrayHasKey('meta', $result);
        $this->assertSame(10, $result['meta']['per_page']);
    }

    public function test_find_all_paginated_filter_by_name(): void
    {
        $this->repository->create(new ProductEntity(id: null, name: 'Item Único', description: null));
        $this->repository->create(new ProductEntity(id: null, name: 'Outro', description: null));

        $result = $this->repository->findAllPaginated(['name' => 'Único']);

        $this->assertCount(1, $result['data']);
        $this->assertSame('Item Único', $result['data'][0]->getName());
    }
}
