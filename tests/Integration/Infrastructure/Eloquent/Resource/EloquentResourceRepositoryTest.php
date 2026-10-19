<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Eloquent\Resource;

use App\Domain\Resource\Entities\Resource as ResourceEntity;
use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\Infrastructure\Eloquent\Resource\EloquentResourceRepository;
use App\Models\Department\Department;
use App\Models\Resource as ResourceModel;
use App\Models\ResourceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EloquentResourceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentResourceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentResourceRepository;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function seedCategoryAndDepartment(): array
    {
        $categoryId = (string) Str::uuid();
        ResourceCategory::forceCreate([
            'id' => $categoryId,
            'name' => 'Equipamentos',
            'description' => null,
        ]);

        $departmentId = (string) Str::uuid();
        Department::forceCreate([
            'id' => $departmentId,
            'department' => 'Produção',
        ]);

        return [$categoryId, $departmentId];
    }

    private function makeEntity(
        ?string $id,
        ?string $categoryId,
        ?string $departmentId,
        string $resourceName = 'Recurso Base',
        string $patrimonialCode = 'PAT-001',
        ?string $notes = null,
    ): ResourceEntity {
        return new ResourceEntity(
            id: $id,
            categoryId: $categoryId,
            departmentId: $departmentId,
            resourceName: $resourceName,
            patrimonialCode: $patrimonialCode,
            manufacturer: null,
            model: null,
            serialNumber: null,
            responsibleId: null,
            notes: $notes,
            documentationLink: null,
            createdAt: null,
            createdBy: null,
            updatedAt: null,
            updatedBy: null,
        );
    }

    public function test_container_binds_resource_repository_interface(): void
    {
        $resolved = $this->app->make(ResourceRepositoryInterface::class);
        $this->assertInstanceOf(EloquentResourceRepository::class, $resolved);
    }

    public function test_create_assigns_uuid_when_id_null(): void
    {
        [$categoryId, $departmentId] = $this->seedCategoryAndDepartment();

        $entity = $this->makeEntity(null, $categoryId, $departmentId);

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('Recurso Base', $created->getResourceName());
        $this->assertSame($categoryId, $created->getCategoryId());

        $row = ResourceModel::query()->find($created->getId());
        $this->assertNotNull($row);
        $this->assertSame('PAT-001', $row->patrimonial_code);
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $found = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($found);
    }

    public function test_update_and_delete_round_trip(): void
    {
        [$categoryId, $departmentId] = $this->seedCategoryAndDepartment();

        $created = $this->repository->create($this->makeEntity(
            null,
            $categoryId,
            $departmentId,
            'Antes',
            'OLD',
        ));

        $updated = $this->repository->update($this->makeEntity(
            id: $created->getId(),
            categoryId: $categoryId,
            departmentId: $departmentId,
            resourceName: 'Depois',
            patrimonialCode: 'NEW',
            notes: 'Obs',
        ));

        $this->assertNotNull($updated);
        $this->assertSame('Depois', $updated->getResourceName());
        $this->assertSame('NEW', $updated->getPatrimonialCode());
        $this->assertSame('Obs', $updated->getNotes());

        $this->assertTrue($this->repository->deleteById((string) $created->getId()));
        $this->assertNull(ResourceModel::query()->find($created->getId()));
    }

    public function test_find_all_paginated_defaults_per_page_to_ten(): void
    {
        [$categoryId, $departmentId] = $this->seedCategoryAndDepartment();

        for ($i = 1; $i <= 11; $i++) {
            $this->repository->create($this->makeEntity(
                null,
                $categoryId,
                $departmentId,
                'Equip '.$i,
                'PCODE-'.$i,
            ));
        }

        $page1 = $this->repository->findAllPaginated([
            'resource_name' => 'Equip',
            'sortField' => 'patrimonial_code',
            'sortDirection' => 'asc',
        ]);

        $this->assertSame(11, $page1['meta']['total']);
        $this->assertCount(10, $page1['data']);
        $this->assertSame(10, $page1['meta']['per_page']);

        $page2 = $this->repository->findAllPaginated([
            'resource_name' => 'Equip',
            'sortField' => 'patrimonial_code',
            'sortDirection' => 'asc',
            'page' => 2,
        ]);

        $this->assertCount(1, $page2['data']);
    }

    public function test_find_all_paginated_search_matches_related_category_name(): void
    {
        [$categoryIdA, $departmentId] = $this->seedCategoryAndDepartment();

        $categoryIdB = (string) Str::uuid();
        ResourceCategory::forceCreate([
            'id' => $categoryIdB,
            'name' => 'Áudio Premium',
            'description' => null,
        ]);

        $this->repository->create($this->makeEntity(
            null,
            $categoryIdA,
            $departmentId,
            'Item Um',
            'A1',
        ));

        $this->repository->create($this->makeEntity(
            null,
            $categoryIdB,
            $departmentId,
            'Item Dois',
            'A2',
        ));

        $result = $this->repository->findAllPaginated(['search' => 'Premium']);
        $this->assertSame(1, $result['meta']['total']);
        $this->assertSame('Item Dois', $result['data'][0]->getResourceName());
    }

    public function test_find_all_paginated_unknown_sort_defaults_to_resource_name(): void
    {
        [$categoryId, $departmentId] = $this->seedCategoryAndDepartment();

        $this->repository->create($this->makeEntity(null, $categoryId, $departmentId, 'Zebra', 'Z1'));
        $this->repository->create($this->makeEntity(null, $categoryId, $departmentId, 'Alpha', 'A1'));

        $result = $this->repository->findAllPaginated([
            'sortField' => 'illegal_column',
            'sortDirection' => 'desc',
        ]);

        $this->assertCount(2, $result['data']);
        $this->assertSame('Zebra', $result['data'][0]->getResourceName());
        $this->assertSame('Alpha', $result['data'][1]->getResourceName());
    }

    public function test_delete_by_id_returns_false_when_row_missing(): void
    {
        $this->assertFalse($this->repository->deleteById('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa'));
    }
}
