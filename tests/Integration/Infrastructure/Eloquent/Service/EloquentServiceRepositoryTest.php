<?php

namespace Tests\Integration\Infrastructure\Eloquent\Service;

use App\Domain\Service\Entities\Service as ServiceEntity;
use App\Infrastructure\Eloquent\Service\EloquentServiceRepository;
use App\Models\Service as ServiceModel;
use App\Models\ServiceUnity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentServiceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentServiceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentServiceRepository();
    }

    public function test_create_service(): void
    {
        $entity = new ServiceEntity(
            id: null,
            service: 'Manutenção Preventiva',
            description: 'Detalhes do serviço'
        );

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('Manutenção Preventiva', $created->getService());
        $this->assertSame('Detalhes do serviço', $created->getDescription());
    }

    public function test_find_by_id_returns_entity(): void
    {
        $created = $this->repository->create(new ServiceEntity(
            id: null,
            service: 'Instalação',
            description: null
        ));

        $found = $this->repository->findById((string) $created->getId());

        $this->assertNotNull($found);
        $this->assertSame($created->getId(), $found->getId());
        $this->assertSame('Instalação', $found->getService());
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $found = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($found);
    }

    public function test_update_persists_fields(): void
    {
        $created = $this->repository->create(new ServiceEntity(
            id: null,
            service: 'Antes',
            description: 'A'
        ));

        $updated = $this->repository->update(new ServiceEntity(
            id: (string) $created->getId(),
            service: 'Depois',
            description: 'B',
            unityId: null,
            createdBy: null,
            updatedBy: null
        ));

        $this->assertNotNull($updated);
        $this->assertSame('Depois', $updated->getService());
        $this->assertSame('B', $updated->getDescription());

        $fromDb = ServiceModel::query()->find($created->getId());
        $this->assertSame('Depois', $fromDb->service);
    }

    public function test_delete_by_id(): void
    {
        $created = $this->repository->create(new ServiceEntity(
            id: null,
            service: 'Remover',
            description: null
        ));

        $deleted = $this->repository->deleteById((string) $created->getId());

        $this->assertTrue($deleted);
        $this->assertNull(ServiceModel::query()->find($created->getId()));
    }

    public function test_find_all_paginated_per_page_is_ten(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->repository->create(new ServiceEntity(
                id: null,
                service: "Serviço {$i}",
                description: null
            ));
        }

        $result = $this->repository->findAllPaginated([]);

        $this->assertSame(10, $result['meta']['per_page']);
        $this->assertCount(10, $result['data']);
        $this->assertSame(12, $result['meta']['total']);
    }

    public function test_find_all_paginated_filter_by_service(): void
    {
        $this->repository->create(new ServiceEntity(id: null, service: 'Consultoria Técnica', description: null));
        $this->repository->create(new ServiceEntity(id: null, service: 'Suporte Básico', description: null));

        $result = $this->repository->findAllPaginated(['service' => 'Consultoria']);

        $this->assertCount(1, $result['data']);
        $this->assertSame('Consultoria Técnica', $result['data'][0]->getService());
    }

    public function test_find_all_paginated_filter_by_created_at(): void
    {
        $this->repository->create(new ServiceEntity(id: null, service: 'Serviço Hoje', description: null));

        $today = date('Y-m-d');
        $result = $this->repository->findAllPaginated(['created_at' => $today]);

        $this->assertGreaterThanOrEqual(1, count($result['data']));
    }

    public function test_find_all_paginated_filter_by_updated_at(): void
    {
        $created = $this->repository->create(new ServiceEntity(id: null, service: 'Editado Hoje', description: null));
        $this->repository->update(new ServiceEntity(
            id: (string) $created->getId(),
            service: 'Editado Hoje',
            description: 'upd'
        ));

        $today = date('Y-m-d');
        $result = $this->repository->findAllPaginated(['updated_at' => $today]);

        $this->assertGreaterThanOrEqual(1, count($result['data']));
    }

    public function test_find_all_paginated_with_search(): void
    {
        $this->repository->create(new ServiceEntity(id: null, service: 'Alpha X', description: 'busca aqui'));
        $this->repository->create(new ServiceEntity(id: null, service: 'Beta Y', description: null));

        $result = $this->repository->findAllPaginated(['search' => 'busca']);

        $this->assertCount(1, $result['data']);
        $this->assertSame('Alpha X', $result['data'][0]->getService());
        $this->assertArrayHasKey('meta', $result);
        $this->assertSame(10, $result['meta']['per_page']);
    }

    public function test_find_all_for_select_orders_by_service_asc(): void
    {
        $this->repository->create(new ServiceEntity(id: null, service: 'Zebra', description: null));
        $this->repository->create(new ServiceEntity(id: null, service: 'Alpha', description: null));
        $this->repository->create(new ServiceEntity(id: null, service: 'Médio', description: null));

        $result = $this->repository->findAllForSelect();

        $this->assertCount(3, $result);
        $this->assertSame('Alpha', $result[0]['label']);
        $this->assertSame('Médio', $result[1]['label']);
        $this->assertSame('Zebra', $result[2]['label']);
        $this->assertArrayHasKey('id', $result[0]);
    }

    public function test_create_and_update_persist_unity_id_and_return_unity_name(): void
    {
        $unityHora = ServiceUnity::query()->create([
            'unity' => 'Hora',
        ]);
        $unityDiaria = ServiceUnity::query()->create([
            'unity' => 'Diária',
        ]);

        $created = $this->repository->create(new ServiceEntity(
            id: null,
            service: 'Iluminação',
            description: 'Palco',
            unityId: (string) $unityHora->id
        ));

        $this->assertSame((string) $unityHora->id, $created->getUnityId());
        $this->assertSame('Hora', $created->getUnity());

        $fromDb = ServiceModel::query()->find($created->getId());
        $this->assertSame((string) $unityHora->id, $fromDb->unity_id);

        $updated = $this->repository->update(new ServiceEntity(
            id: (string) $created->getId(),
            service: 'Iluminação',
            description: 'Palco',
            unityId: (string) $unityDiaria->id
        ));

        $this->assertNotNull($updated);
        $this->assertSame((string) $unityDiaria->id, $updated->getUnityId());
        $this->assertSame('Diária', $updated->getUnity());

        $listed = $this->repository->findAllPaginated([]);
        $this->assertCount(1, $listed['data']);
        $this->assertSame('Diária', $listed['data'][0]->getUnity());
    }
}
