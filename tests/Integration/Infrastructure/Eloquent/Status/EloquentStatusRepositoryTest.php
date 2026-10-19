<?php

namespace Tests\Integration\Infrastructure\Eloquent\Status;

use App\Domain\Status\Entities\Status as StatusEntity;
use App\Domain\Status\Enums\StatusType;
use App\Infrastructure\Eloquent\Status\EloquentStatusRepository;
use App\Models\Status as StatusModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentStatusRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentStatusRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentStatusRepository();
    }

    public function test_create_status(): void
    {
        $entity = new StatusEntity(
            id: null,
            status: 'Criado no teste',
            type: StatusType::JOB,
            observacoes: 'Observação'
        );

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('Criado no teste', $created->getStatus());
        $this->assertSame(StatusType::JOB, $created->getType());
        $this->assertSame('Observação', $created->getObservacoes());
    }

    public function test_find_by_id_returns_entity(): void
    {
        $created = $this->repository->create(new StatusEntity(
            id: null,
            status: 'Encontrar',
            type: StatusType::JOB,
            observacoes: null
        ));

        $found = $this->repository->findById((string) $created->getId());

        $this->assertNotNull($found);
        $this->assertSame($created->getId(), $found->getId());
        $this->assertSame('Encontrar', $found->getStatus());
        $this->assertSame(StatusType::JOB, $found->getType());
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $found = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($found);
    }

    public function test_update_job_status_sets_type_job_and_persists_fields(): void
    {
        $created = $this->repository->create(new StatusEntity(
            id: null,
            status: 'Antes',
            type: StatusType::JOB,
            observacoes: 'A'
        ));

        $updated = $this->repository->update(new StatusEntity(
            id: (string) $created->getId(),
            status: 'Depois',
            type: StatusType::JOB,
            observacoes: 'B'
        ));

        $this->assertNotNull($updated);
        $this->assertSame('Depois', $updated->getStatus());
        $this->assertSame(StatusType::JOB, $updated->getType());
        $this->assertSame('B', $updated->getObservacoes());

        $row = StatusModel::query()->find($created->getId());
        $this->assertSame(StatusType::JOB->value, (int) $row->type);
        $this->assertSame('Depois', $row->status);
        $this->assertSame('B', $row->observacoes);
    }

    public function test_update_returns_null_for_non_job_record(): void
    {
        $model = StatusModel::create([
            'status' => 'Pedido',
            'type' => StatusType::ORDER->value,
            'observacoes' => null,
        ]);

        $result = $this->repository->update(new StatusEntity(
            id: (string) $model->id,
            status: 'X',
            type: StatusType::JOB,
            observacoes: null
        ));

        $this->assertNull($result);
        $this->assertSame('Pedido', $model->fresh()->status);
    }

    public function test_update_returns_null_when_id_empty(): void
    {
        $this->assertNull($this->repository->update(new StatusEntity(
            id: null,
            status: 'X',
            type: StatusType::JOB,
            observacoes: null
        )));
    }

    public function test_delete_by_id_deletes_only_job_records(): void
    {
        $job = $this->repository->create(new StatusEntity(
            id: null,
            status: 'Excluir Job',
            type: StatusType::JOB,
            observacoes: null
        ));

        $this->assertTrue($this->repository->deleteById((string) $job->getId()));
        $this->assertNull(StatusModel::query()->find($job->getId()));

        $order = StatusModel::create([
            'status' => 'Pedido',
            'type' => StatusType::ORDER->value,
            'observacoes' => null,
        ]);

        $this->assertFalse($this->repository->deleteById((string) $order->id));
        $this->assertNotNull($order->fresh());
    }

    public function test_delete_by_id_returns_false_when_missing_or_empty(): void
    {
        $this->assertFalse($this->repository->deleteById(''));
        $this->assertFalse($this->repository->deleteById('00000000-0000-0000-0000-000000000000'));
    }

    public function test_find_all_by_type_applies_search_on_status_or_observacoes(): void
    {
        $this->repository->create(new StatusEntity(
            id: null,
            status: 'Aguardando Aprovação',
            type: StatusType::JOB,
            observacoes: 'Sem observações'
        ));

        $this->repository->create(new StatusEntity(
            id: null,
            status: 'Em Progresso',
            type: StatusType::JOB,
            observacoes: 'Cliente pediu urgência'
        ));

        $this->repository->create(new StatusEntity(
            id: null,
            status: 'Finalizado',
            type: StatusType::JOB,
            observacoes: null
        ));

        $resultByStatus = $this->repository->findAllByType([
            'type' => 'job',
            'search' => 'Progresso',
            'sortField' => 'id',
            'sortDirection' => 'asc',
        ]);

        $this->assertCount(1, $resultByStatus['data']);
        $this->assertSame('Em Progresso', $resultByStatus['data'][0]->getStatus());

        $resultByObservacoes = $this->repository->findAllByType([
            'type' => 'job',
            'search' => 'urgência',
            'sortField' => 'id',
            'sortDirection' => 'asc',
        ]);

        $this->assertCount(1, $resultByObservacoes['data']);
        $this->assertSame('Em Progresso', $resultByObservacoes['data'][0]->getStatus());
    }

    public function test_find_all_by_type_applies_field_filters_and_sorting(): void
    {
        $s1 = $this->repository->create(new StatusEntity(
            id: null,
            status: 'Beta',
            type: StatusType::JOB,
            observacoes: 'Obs 1'
        ));

        $s2 = $this->repository->create(new StatusEntity(
            id: null,
            status: 'Alpha',
            type: StatusType::JOB,
            observacoes: 'Obs 2'
        ));

        // Garante que registros de outro tipo não vazem
        StatusModel::create([
            'status' => 'Pedido',
            'type' => StatusType::ORDER->value,
            'observacoes' => 'Obs pedido',
        ]);

        $filteredByStatus = $this->repository->findAllByType([
            'type' => 'job',
            'status' => 'Alp',
            'sortField' => 'id',
            'sortDirection' => 'asc',
        ]);

        $this->assertCount(1, $filteredByStatus['data']);
        $this->assertSame($s2->getId(), $filteredByStatus['data'][0]->getId());

        $filteredByObservacoes = $this->repository->findAllByType([
            'type' => 'job',
            'observacoes' => 'Obs 1',
            'sortField' => 'id',
            'sortDirection' => 'asc',
        ]);

        $this->assertCount(1, $filteredByObservacoes['data']);
        $this->assertSame($s1->getId(), $filteredByObservacoes['data'][0]->getId());

        $sortedByStatusAsc = $this->repository->findAllByType([
            'type' => 'job',
            'sortField' => 'status',
            'sortDirection' => 'asc',
        ]);

        $this->assertGreaterThanOrEqual(2, count($sortedByStatusAsc['data']));
        $this->assertSame('Alpha', $sortedByStatusAsc['data'][0]->getStatus());
        $this->assertSame('Beta', $sortedByStatusAsc['data'][1]->getStatus());

        $sortedByStatusDesc = $this->repository->findAllByType([
            'type' => 'job',
            'sortField' => 'status',
            'sortDirection' => 'desc',
        ]);

        $this->assertGreaterThanOrEqual(2, count($sortedByStatusDesc['data']));
        $this->assertSame('Beta', $sortedByStatusDesc['data'][0]->getStatus());
        $this->assertSame('Alpha', $sortedByStatusDesc['data'][1]->getStatus());
    }
}

