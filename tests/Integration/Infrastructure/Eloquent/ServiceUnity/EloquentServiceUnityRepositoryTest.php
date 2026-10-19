<?php

namespace Tests\Integration\Infrastructure\Eloquent\ServiceUnity;

use App\Domain\ServiceUnity\Entities\ServiceUnity;
use App\Infrastructure\Eloquent\ServiceUnity\EloquentServiceUnityRepository;
use App\Models\ServiceUnity as ModelsServiceUnity;
use App\Models\User;
use Database\Seeders\UserAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentServiceUnityRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentServiceUnityRepository $repository;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentServiceUnityRepository();
        $this->seed(UserAdminSeeder::class);
        $this->user = User::query()
            ->where('email', 'admin@example.com')
            ->firstOrFail();
    }

    public function test_create_service_unity(): void
    {
        $entity = new ServiceUnity(
            id: null,
            unity: 'Câmera Frontal',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        );

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('Câmera Frontal', $created->getUnity());
        $this->assertSame((int) $this->user->id, $created->getCreator());
        $this->assertNull($created->getEditor());
    }

    public function test_find_by_id_with_result(): void
    {
        $created = $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Searched Service Unity',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $found = $this->repository->findById((string) $created->getId());

        $this->assertNotNull($found);
        $this->assertSame($created->getId(), $found->getId());
        $this->assertSame((int) $this->user->id, $found->getCreator());
        $this->assertNull($found->getEditor());
        $this->assertSame('Searched Service Unity', $found->getUnity());
    }

    public function test_bind_by_id_returns_null(): void
    {
        $found = $this->repository->findById('0000000-0000000-00000000-000000-0000-000000000');
        $this->assertNull($found);
    }

    public function test_update_persists_fields(): void
    {
        $created = $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Before',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $updated = $this->repository->update(new ServiceUnity(
            id: (string) $created->getId(),
            unity: 'After',
            createdBy: $created->getCreator(),
            updatedBy: (int) $this->user->id,
            createdAt: $created->getCreatedAt(),
            updatedAt: null
        ));

        $this->assertNotNull($updated);
        $this->assertSame('After', $updated->getUnity());
        $this->assertSame((int) $this->user->id, $updated->getCreator());
        $this->assertSame((int) $this->user->id, $updated->getEditor());
    }

    public function test_delete_by_id(): void
    {
        $created = $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Remove',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $deleted = $this->repository->deleteById((string) $created->getId());

        $this->assertTrue($deleted);
        $this->assertNull(ModelsServiceUnity::query()->find($created->getId()));
    }

    public function test_find_all_paginated_with_search(): void
    {
        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'First Register',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Second Register',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $result = $this->repository->findAllPaginated(['search' => 'First Register']);

        $this->assertCount(1, $result['data']);
        $this->assertSame('First Register', $result['data'][0]->getUnity());
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('current_page', $result['meta']);
        $this->assertArrayHasKey('per_page', $result['meta']);
        $this->assertArrayHasKey('total', $result['meta']);
        $this->assertArrayHasKey('links', $result['meta']);
        $this->assertSame(10, $result['meta']['per_page']);
    }

    public function test_find_all_paginated_filter_by_unity(): void
    {
        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'First Unity',
            createdBy: (int) $this->user->id,
            updatedBy: (int) $this->user->id,
            createdAt: null,
            updatedAt: null
        ));

        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Second Unity',
            createdBy: (int) $this->user->id,
            updatedBy: (int) $this->user->id,
            createdAt: null,
            updatedAt: null
        ));

        $result = $this->repository->findAllPaginated(['unity' => 'First Unity']);

        $this->assertCount(1, $result['data']);
        $this->assertSame('First Unity', $result['data'][0]->getUnity());
    }

    public function test_find_all_paginated_filter_by_created_at(): void
    {
        $today = now()->format('Y-m-d');

        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Unity Created Today',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $result = $this->repository->findAllPaginated(['created_at' => $today]);

        $this->assertGreaterThanOrEqual(1, count($result['data']));

        $unities = array_map(fn ($entity) => $entity->getUnity(), $result['data']);
        $this->assertContains('Unity Created Today', $unities);
    }

    public function test_find_all_paginated_filter_by_updated_at(): void
    {
        $today = now()->format('Y-m-d');

        $created = $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Unity Updated Today',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $this->repository->update(new ServiceUnity(
            id: (string) $created->getId(),
            unity: 'Unity Updated Today',
            createdBy: $created->getCreator(),
            updatedBy: (int) $this->user->id,
            createdAt: $created->getCreatedAt(),
            updatedAt: null
        ));

        $result = $this->repository->findAllPaginated(['updated_at' => $today]);

        $this->assertGreaterThanOrEqual(1, count($result['data']));

        $unities = array_map(fn ($entity) => $entity->getUnity(), $result['data']);
        $this->assertContains('Unity Updated Today', $unities);
    }

    public function test_find_all_paginated_sort_by_created_at(): void
    {
        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Alpha Unity',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Beta Unity',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $result = $this->repository->findAllPaginated([
            'sortField'    => 'created_at',
            'sortDirection' => 'asc',
        ]);

        $this->assertGreaterThanOrEqual(2, count($result['data']));
        $this->assertSame(10, $result['meta']['per_page']);
    }

    public function test_find_all_for_select_orders_by_unity_asc_and_returns_label(): void
    {
        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Zebra',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Alpha',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $this->repository->create(new ServiceUnity(
            id: null,
            unity: 'Médio',
            createdBy: (int) $this->user->id,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        ));

        $result = $this->repository->findAllForSelect();

        $this->assertCount(3, $result);
        $this->assertSame('Alpha', $result[0]['label']);
        $this->assertSame('Médio', $result[1]['label']);
        $this->assertSame('Zebra', $result[2]['label']);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayNotHasKey('unity', $result[0]);
    }
}
