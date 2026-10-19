<?php

namespace Tests\Integration\UseCase\Status;

use App\Domain\Status\Entities\Status as StatusEntity;
use App\Domain\Status\Enums\StatusType;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\UseCase\Status\DeleteJobStatusUseCase;
use App\Models\Status as StatusModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteJobStatusUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private StatusRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(StatusRepositoryInterface::class);
    }

    public function test_delete_job_status_deletes_record_and_returns_true(): void
    {
        $created = $this->repository->create(new StatusEntity(
            id: null,
            status: 'Para excluir',
            type: StatusType::JOB,
            observacoes: null
        ));

        $useCase = new DeleteJobStatusUseCase($this->repository);
        $result = $useCase->execute((string) $created->getId());

        $this->assertTrue($result);
        $this->assertNull(StatusModel::query()->find($created->getId()));
    }

    public function test_delete_job_status_returns_false_for_non_job_record(): void
    {
        $model = StatusModel::create([
            'status' => 'Pedido',
            'type' => StatusType::ORDER->value,
            'observacoes' => null,
        ]);

        $useCase = new DeleteJobStatusUseCase($this->repository);
        $result = $useCase->execute((string) $model->id);

        $this->assertFalse($result);
        $this->assertNotNull($model->fresh());
    }
}

