<?php

namespace Tests\Integration\Infrastructure\Eloquent\ComissionedAgent;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent as ComissionedAgentEntity;
use App\Infrastructure\Eloquent\ComissionedAgent\EloquentComissionedAgentRepository;
use App\Models\ComissionedAgent as ComissionedAgentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentComissionedAgentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentComissionedAgentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentComissionedAgentRepository();
    }

    private function makeEntity(array $overrides = []): ComissionedAgentEntity
    {
        return new ComissionedAgentEntity(
            id: $overrides['id'] ?? null,
            name: $overrides['name'] ?? 'Carlos Alberto',
            defaultComission: $overrides['defaultComission'] ?? 10.0,
            paymentDays: $overrides['paymentDays'] ?? 30,
            observations: $overrides['observations'] ?? null,
        );
    }

    public function test_create_comissioned_agent(): void
    {
        $entity = $this->makeEntity(['name' => 'Carlos Alberto', 'defaultComission' => 15.5, 'paymentDays' => 45]);

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('Carlos Alberto', $created->getName());
        $this->assertSame(15.5, $created->getDefaultComission());
        $this->assertSame(45, $created->getPaymentDays());
    }

    public function test_find_by_id_returns_entity(): void
    {
        $created = $this->repository->create($this->makeEntity(['name' => 'Fernanda Lima']));

        $found = $this->repository->findById((string) $created->getId());

        $this->assertNotNull($found);
        $this->assertSame($created->getId(), $found->getId());
        $this->assertSame('Fernanda Lima', $found->getName());
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $found = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($found);
    }

    public function test_update_persists_fields(): void
    {
        $created = $this->repository->create($this->makeEntity(['name' => 'Carlos Alberto', 'defaultComission' => 10.0, 'paymentDays' => 30]));

        $updated = $this->repository->update(new ComissionedAgentEntity(
            id: (string) $created->getId(),
            name: 'Carlos Atualizado',
            defaultComission: 20.0,
            paymentDays: 60,
            observations: 'Atualizado',
        ));

        $this->assertNotNull($updated);
        $this->assertSame('Carlos Atualizado', $updated->getName());
        $this->assertSame(20.0, $updated->getDefaultComission());
        $this->assertSame(60, $updated->getPaymentDays());
        $this->assertSame('Atualizado', $updated->getObservations());

        $fromDb = ComissionedAgentModel::query()->find($created->getId());
        $this->assertSame('Carlos Atualizado', $fromDb->name);
        $this->assertEquals(20.0, $fromDb->default_comission);
        $this->assertSame(60, $fromDb->payment_days);
    }

    public function test_delete_by_id(): void
    {
        $created = $this->repository->create($this->makeEntity());

        $deleted = $this->repository->deleteById((string) $created->getId());

        $this->assertTrue($deleted);
        $this->assertNull(ComissionedAgentModel::query()->find($created->getId()));
    }

    public function test_delete_by_id_returns_false_for_missing(): void
    {
        $result = $this->repository->deleteById('00000000-0000-0000-0000-000000000000');
        $this->assertFalse($result);
    }

    public function test_find_all_paginated_returns_meta(): void
    {
        $this->repository->create($this->makeEntity());

        $result = $this->repository->findAllPaginated();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertSame(10, $result['meta']['per_page']);
        $this->assertCount(1, $result['data']);
    }

    public function test_find_all_paginated_with_filter_by_name(): void
    {
        $this->repository->create($this->makeEntity(['name' => 'Carlos Alberto', 'defaultComission' => 5.0, 'paymentDays' => 10]));
        $this->repository->create($this->makeEntity(['name' => 'Fernanda Lima', 'defaultComission' => 8.0, 'paymentDays' => 20]));

        $result = $this->repository->findAllPaginated(['name' => 'Carlos']);

        $this->assertCount(1, $result['data']);
        $this->assertSame('Carlos Alberto', $result['data'][0]->getName());
    }

    public function test_find_all_paginated_with_filter_by_default_comission(): void
    {
        $this->repository->create($this->makeEntity(['name' => 'Carlos Alberto', 'defaultComission' => 5.0, 'paymentDays' => 10]));
        $this->repository->create($this->makeEntity(['name' => 'Fernanda Lima', 'defaultComission' => 12.5, 'paymentDays' => 20]));

        $result = $this->repository->findAllPaginated(['defaultComission' => 12.5]);

        $this->assertCount(1, $result['data']);
        $this->assertSame(12.5, $result['data'][0]->getDefaultComission());
    }

    public function test_find_all_paginated_with_filter_by_payment_days(): void
    {
        $this->repository->create($this->makeEntity(['name' => 'Carlos Alberto', 'defaultComission' => 5.0, 'paymentDays' => 10]));
        $this->repository->create($this->makeEntity(['name' => 'Fernanda Lima', 'defaultComission' => 8.0, 'paymentDays' => 30]));

        $result = $this->repository->findAllPaginated(['paymentDays' => 30]);

        $this->assertCount(1, $result['data']);
        $this->assertSame(30, $result['data'][0]->getPaymentDays());
    }

    public function test_find_all_paginated_sort_by_name_runs_without_sql_error(): void
    {
        $this->repository->create($this->makeEntity(['name' => 'Zeca', 'defaultComission' => 5.0, 'paymentDays' => 10]));
        $this->repository->create($this->makeEntity(['name' => 'Ana', 'defaultComission' => 8.0, 'paymentDays' => 20]));

        $result = $this->repository->findAllPaginated([
            'sortField' => 'name',
            'sortDirection' => 'asc',
        ]);

        $this->assertCount(2, $result['data']);
        $this->assertSame('Ana', $result['data'][0]->getName());
        $this->assertSame('Zeca', $result['data'][1]->getName());
    }
}
